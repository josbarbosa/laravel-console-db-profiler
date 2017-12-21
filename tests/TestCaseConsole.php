<?php namespace PackageTests;

use JosBarbosa\ConsoleDbProfiler\ConsoleDbProfilerServiceProvider;
use PackageTests\Utilities\TestOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Mockery as m;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;
use Illuminate\Foundation\Application;

/**
 * Class TestCaseConsole
 * @package PackageTests
 */
class TestCaseConsole extends TestCase
{
    /**
     * @var TestOutput $output
     */
    protected $output;

    /**
     * @var \Mockery $application
     */
    protected $application;

    /**
     * @var ConsoleDbProfilerServiceProvider $provider
     */
    protected $provider;

    protected function setUp()
    {
        parent::setUp();

        $this->output = new TestOutput();
        app()->instance(ConsoleOutput::class, $this->output);

        $this->application = m::mock(Application::class)->makePartial();
        $this->provider = new ConsoleDbProfilerServiceProvider($this->application);
    }

    /**
     * @param string $environment
     * @return $this
     */
    function setEnv(string $environment): self
    {
        $this->application->env = $environment;
        return $this;
    }

    /**
     * @param bool $isRunning
     * @return $this
     */
    function runningInConsole(bool $isRunning): self
    {
        $this->application->shouldReceive('runningInConsole')->times(2)->withNoArgs()->andReturn($isRunning);
        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    function setConfig(string $key, $value): self
    {
        h::setConfig($key, $value);
        return $this;
    }

    /**
     * @return $this
     */
    function boot(): self
    {
        $this->provider->register();
        $this->provider->boot();

        return $this;
    }

    protected function tearDown()
    {
        parent::tearDown();
        app()->instance(ConsoleOutput::class, new ConsoleOutput());
    }
}
