<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Friends;
use Doctrine\ORM;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private $session;
    public function __construct()
    {
        $this->session = new Session();
    }
    /**
     * @Route("/login")
     */
    public function indexAction(Request $request)
    {
        $session = $this->session->get('name');
        if (!isset($session)) {
            if (isset($_POST['login']) && isset($_POST['password']) &&
                strlen($_POST['login']) > 2 && strlen($_POST['password']) >= 4
            ) {
                $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
                $product = $repository->findOneBy(
                    array('login' => $_POST['login'], 'password' => sha1($_POST['password']))
                );
                if ($product != null) {
                    $this->session->set('name', $_POST['login']);
                    return $this->redirect('/home');
                } else {
                    $this->get('session')->getFlashBag()->add('error',
                        'Une erreur est survenue veuillez vérifier votre saisie !');
                    return $this->render('default/index.html.twig', [
                        'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                    ]);
                }
            } else {
                return $this->render('default/index.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                ]);
            }
        }
        else {
            return $this->redirect('/home');
        }
    }

    /**
     * @Route("/register")
     */
    public function RegisterAction(Request $request)
    {
        $session = $this->session->get('name');
        if (!isset($session)) {
            if (isset($_POST['login']) && isset($_POST['password']) &&
                strlen($_POST['login']) > 2 && strlen($_POST['password']) >= 4 &&
                $_POST['password'] == $_POST['password_confirm']
            ) {
                $product = new Users();
                $product->setLogin($_POST['login']);
                $product->setPassword(sha1($_POST['password']));
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
                $this->redirect('/login');
                $this->get('session')->getFlashBag()->add('success',
                    'Inscription réussi !');
                return $this->redirect('/login');
            } else {
                return $this->render('default/register.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                ]);
            }
        }
        else {
            return $this->redirect('/home');
        }
    }
}
