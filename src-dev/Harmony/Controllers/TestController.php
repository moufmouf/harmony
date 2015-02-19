<?php
/*
 * This file is part of the Mouf core package.
 *
 * (c) 2012 David Negrier <david@mouf-php.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */
namespace Harmony\Controllers;

use Harmony\Services\FileNotWritableException;
use Harmony\Services\FileService;
use Mouf\Html\Renderer\Twig\MoufTwigEnvironment;
use Mouf\Html\Renderer\Twig\TwigTemplate;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Utils\WebLibraryManager\WebLibrary;
use Mouf\Mvc\Splash\Controllers\Controller;
use Mouf\Mvc\Splash\HtmlResponse;
use Mouf\Security\UserFileDao\UserFileBean;
use Mouf\Security\UserFileDao\UserFileDao;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * This controller displays the Mouf install process (when Mouf is started the first time).
 *
 */
class TestController extends Controller {

	/**
	 * The template used by the main page for mouf.
	 *
	 * @var TemplateInterface
	 */
	private $template;
	
	/**
	 * The content block the template will be writting into.
	 *
	 * @var HtmlBlock
	 */
	private $contentBlock;

	/**
	 * The content block the template will be writting into.
	 *
	 * @var HtmlBlock
	 */
	private $leftBlock;

	/**
	 * @var MoufTwigEnvironment
	 */
	private $twigEnvironment;


	/**
	 * @param TemplateInterface $template
	 * @param HtmlBlock $contentBlock
	 * @param HtmlBlock $leftBlock
	 * @param MoufTwigEnvironment $twigEnvironment
	 */
	public function __construct(TemplateInterface $template, HtmlBlock $contentBlock, HtmlBlock $leftBlock, MoufTwigEnvironment $twigEnvironment)
	{
		$this->template = $template;
		$this->contentBlock = $contentBlock;
		$this->leftBlock = $leftBlock;
		$this->twigEnvironment = $twigEnvironment;
	}


	/**
	 *
	 * @URL test/
	 */
	public function index() {

		$this->template->getWebLibraryManager()->addJsFile('src-dev/views/javascript/autobahn/autobahn.min.js');
		$this->template->getWebLibraryManager()->addJsFile('components/angularjs/angular.js');
		$this->template->getWebLibraryManager()->addJsFile('src-dev/views/javascript/console-app.js');
		$this->template->getWebLibraryManager()->addJsFile('src-dev/views/javascript/console-directive.js');
		$this->template->getWebLibraryManager()->addJsFile('src-dev/views/javascript/console-controller.js');

		$this->template->getWebLibraryManager()->addCssFile('src-dev/views/javascript/console.css');

		/*$this->contentBlock->addHtmlElement(new TwigTemplate($this->twigEnvironment, 'src-dev/views/harmony_installer/welcome.twig',
			array(
				"isUserfileAvailable"=>$this->userFileDao->isUserFileAvailable(),
				"harmonyUsersFile"=>$harmonyUsersFile,
			)));*/

		/*$this->leftBlock->addText('
		<button class="btn btn-success" id="tail error log" type="button">Tail error log</button>
		<script>
		jQuery.ajax("http://localhost:8001/run?name=tail_error_log&command=tail -f /var/log/apache2/error.log");

</script>
		');*/

		$this->contentBlock->addText('<div ng-app="consoleApp">

<div ng-controller="ConsoleController">


        <div ng-repeat="console in consoles">
        	<console object="console" kill="kill(name)" remove="remove(name)"></console>
        </div>

</div>

</div>');
		return new HtmlResponse($this->template);
	}

}