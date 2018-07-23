<?php declare(strict_types = 1);

/**
 * @Test: Contributte/Deployer/Runner
 */

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\Section;
use Contributte\Deployer\Runner;
use Deployment\Server;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(function (): void {
	$localFiles = [];

	$config = new Config();
	$config->addSection($section = new Section());
	$section->setName('TEST');
	$section->setLocal(FIXTURES_DIR);
	$section->setRemote('foobar');
	$config->setTempDir(TEMP_DIR);

	$server = Mockery::mock(Server::class);
	$server->shouldReceive('connect');
	$server->shouldReceive('getDir');
	$server->shouldReceive('createDir');
	$server->shouldReceive('writeFile')
		->andReturnUsing(function ($local, $remote) use (&$localFiles): void {
			$localFiles[] = $remote;
		})->times(4);
	$server->shouldReceive('renameFile')
		->times(3);
	$server->shouldReceive('removeFile');
	$server->shouldReceive('readFile')
		->once()
		->andReturnUsing(function ($remoteFile, $localFile): void {
			file_put_contents($localFile, gzdeflate(sprintf('%s', time()), 9));
		});

	$runner = Mockery::mock(Runner::class);
	$runner->shouldAllowMockingProtectedMethods();
	$runner->makePartial();
	$runner->shouldReceive('createServer')->andReturn($server);

	$runner->run($config);

	Assert::equal([
		'/.htdeployment.running',
		'/bar.log.deploytmp',
		'/foo.txt.deploytmp',
		'/.htdeployment.deploytmp',
	], $localFiles);

	$server->mockery_verify();
});
