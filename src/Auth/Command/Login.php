<?php

namespace Profounder\Auth\Command;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profounder\Core\Console\ContainerAwareCommand;
use Profounder\Service\Identity\Identity;
use Profounder\Service\Identity\IdentityContract;
use Profounder\Auth\Session\Session;
use Profounder\Auth\Session\StoreContract;
use Profounder\Auth\Http\RequestContract;
use Profounder\Auth\Http\Parser\ParserContract;

class Login extends ContainerAwareCommand
{
    /**
     * Parser instance.
     *
     * @var ParserContract
     */
    private $parser;

    /**
     * Request instance.
     *
     * @var RequestContract
     */
    private $request;

    /**
     * Store instance.
     *
     * @var StoreContract
     */
    private $store;

    /**
     * Login constructor.
     *
     * @param RequestContract $request
     * @param ParserContract  $parser
     * @param StoreContract   $store
     */
    public function __construct(RequestContract $request, ParserContract $parser, StoreContract $store)
    {
        $this->store   = $store;
        $this->parser  = $parser;
        $this->request = $request;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('profounder:login')
            ->setDescription('Logins to profound.com to retrieve and store the session for subsequent requests.')
            ->addOption('username', 'u', InputOption::VALUE_OPTIONAL, 'Username to act as.')
            ->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'Corresponding username password.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->dispatchLoginRequest($this->getIdentity($input));

        $this->extractAndStoreSession($response)
            ? $output->writeln('Successfully logged-in and stored the session.')
            : $output->writeln('<error>Could not login; please try again.</>');
    }

    /**
     * Dispatches login request and returns the response.
     *
     * @param IdentityContract $identity
     *
     * @return ResponseInterface
     */
    private function dispatchLoginRequest(IdentityContract $identity = null)
    {
        if ($identity) {
            $this->request->actAs($identity);
        }

        return $this->request->dispatch();
    }

    /**
     * Creates an Identity instance from the input options.
     *
     * @param InputInterface $input
     *
     * @return Identity|null
     */
    private function getIdentity(InputInterface $input)
    {
        if ($username = $input->getOption('username') && $password = $input->getOption('password')) {
            return Identity::createWithCredentials($username, $password);
        }

        return null;
    }

    /**
     * Extracts the session from the response and stores it.
     *
     * @param ResponseInterface $response
     *
     * @return bool
     */
    private function extractAndStoreSession(ResponseInterface $response)
    {
        return $this->store->save($this->extractSession($response));
    }

    /**
     * Extracts a Session instance from the response.
     *
     * @param ResponseInterface $response
     *
     * @return Session
     */
    private function extractSession(ResponseInterface $response)
    {
        return $this->parser->parse($response);
    }
}
