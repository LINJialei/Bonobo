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

class DefaultController extends Controller
{
    private $session;
    public function __construct()
    {
        $this->session = new Session();
    }
    /**
     * @Route("/", name="login")
     */
    public function indexAction(Request $request)
    {
        $session = $this->session->get('name');
        if (!isset($session)) {
            return $this->render('default/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            ]);
        }
        else {
            return $this->redirect('/home');
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function LogoutAction(Request $request)
    {
        $session = $this->session->get('name');
        if (isset($session)) {
            $this->session->remove('name');
            return $this->redirect('/');
        }
        else {
            return $this->redirect('/');
        }
    }

    /**
     * @Route("/register")
     */
    public function RegisterAction(Request $request)
    {
        $session = $this->session->get('name');
        if (!isset($session)) {
            return $this->render('default/register.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            ]);
        }
        else {
            return $this->redirect('/home');
        }
    }

    /**
     * @Route("/home", name="home")
     */
    public function HomeAction(Request $request)
    {
        $session = $this->session->get('name');
        if (isset($session)) {
            return $this->render('default/home.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            ]);
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Vous devez etre connecté pour accedez a cette page.');
            return $this->redirect('/');
        }
    }

    /**
     * @Route("/account", name="account")
     */
    public function AccountAction(Request $request)
    {
        $session = $this->session->get('name');
        if (isset($session)) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
            $product = $repository->findOneBy(
                array('login' => $this->session->get('name')));
            if ($product != null) {
                if (isset($_POST['login'])) {
                    $check = false;
                    foreach ($_POST as $key => $value) {
                        if ($value != "") {
                            $check = true;
                        }
                        else {
                            $check = false;
                        }
                    }
                    if ($check) {
                        $em = $this->getDoctrine()->getManager();
                        $product = $em->getRepository('AppBundle:Users')->findOneBy(
                            array('login' => $this->session->get('name')));
                        $product->setLogin($_POST['login']);
                        $product->setPassword(sha1($_POST['password']));
                        $product->setFirstname($_POST['firstname']);
                        $product->setLastname($_POST['lastname']);
                        $product->setBirthday(new \Datetime($_POST['birthday']));
                        $product->setFamily($_POST['family']);
                        $product->setRace($_POST['race']);
                        $product->setFoods($_POST['foods']);
                        $em->flush();
                    }
                    else {
                        $this->get('session')->getFlashBag()->add('error',
                            'Une erreur est survenue, veuillez vérifier votre saisie.');
                        return $this->redirect('/account');
                    }
                }
                return $this->render('default/account.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                    'user_info' => $product,
                ]);
            }
            else {
                $this->get('session')->getFlashBag()->add('error',
                    'Vous n\'etes pas autorisé a accedez a cette page.');
                return $this->redirect('/home');
            }
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Vous devez etre connecté pour accedez a cette page.');
            return $this->redirect('/');
        }
    }

    /**
     * @Route("/list", name="list")
     */
    public function ListAction(Request $request)
    {
        $session = $this->session->get('name');
        if (isset($session)) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
            $product = $repository->createQueryBuilder('u')
                ->where('u.login != :login')
                ->setParameter('login', $session)
                ->getQuery()
                ->getResult();
            return $this->render('default/list.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'friends' => $product,
            ]);
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Vous devez etre connecté pour accedez a cette page.');
            return $this->redirect('/');
        }
    }

    /**
     * @Route("/get/{id}", name="getuser")
     */
    public function GetAction(Request $request, $id)
    {
        $session = $this->session->get('name');
        if (isset($session)) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
            $product = $repository->findOneBy(['id' => $id]);
            if ($product != null) {
                return $this->render('default/getuser.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                    'friends' => $product,
                ]);
            }
            else {
                $this->get('session')->getFlashBag()->add('error',
                    'l\'utilisateur n\'a pas été trouver.');
                return $this->redirect('/');
            }
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Vous devez etre connecté pour accedez a cette page.');
            return $this->redirect('/');
        }
    }

    /**
     * @Route("/add/{id}", name="addfriend")
     */
    public function AddAction(Request $request, $id)
    {
        $session = $this->session->get('name');
        if (isset($session)) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
            $product = $repository->findOneBy(['login' => $this->session->get('name')]);
            if ($product != null) {
                $repos = $this->getDoctrine()->getRepository('AppBundle:Friends');
                $friends = $repos->findOneBy(
                    array('idAuth' => $product->getId(), 'idDest' => $id));
                if ($friends != null) {
                    $this->get('session')->getFlashBag()->add('error',
                        'Ce bonobo est deja dans votre liste d\'amis.');
                    return $this->redirect('/list');
                }
                else {
                    $em = $this->getDoctrine()->getManager();
                    $newfriend = $em->getRepository('AppBundle:Friends');
                    $newfriend = new Friends();
                    $newfriend->setIdAuth($product->getId());
                    $newfriend->setIdDest($id);
                    $em->persist($newfriend);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('success',
                        'Ce bonobo a été ajoutez a votre liste d\'amis.');
                    return $this->redirect('/list');
                }
            }
            else {
                $this->get('session')->getFlashBag()->add('error',
                    'Vous devez etre connecté pour accedez a cette page.');
                return $this->redirect('/');
            }
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Vous devez etre connecté pour accedez a cette page.');
            return $this->redirect('/');
        }
    }

    /**
     * @Route("/friend", name="friend")
     */
    public function FriendsAction(Request $request)
    {
        $session = $this->session->get('name');
        if (isset($session)) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
            $product = $repository->findOneBy(['login' => $session]);
            if ($product != null) {
                $em = $this->getDoctrine()->getManager();
                $repos = $this->getDoctrine()->getRepository('AppBundle:Friends');
                $friends = $repos->findBy(
                    array('idAuth' => $product->getId()));
                $tab_friends = [];
                foreach ($friends as $key => $value) {
                    $reposi = $this->getDoctrine()->getRepository('AppBundle:Users');
                    $friend = $reposi->findOneBy(
                        array('id' => $value->getIdDest()));
                    if ($friend != null) {
                        array_push($tab_friends, $friend);
                    }
                }
                return $this->render('default/friends.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                    'friends' => $tab_friends,
                ]);
            }
            else {
                $this->get('session')->getFlashBag()->add('error',
                    'Vous devez etre connecté pour accedez a cette page.');
                return $this->redirect('/');
            }
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Vous devez etre connecté pour accedez a cette page.');
            return $this->redirect('/');
        }
    }

    /**
     * @Route("/delete/{id}", name="deletefriend")
     */
    public function DeleteAction(Request $request, $id)
    {
        $session = $this->session->get('name');
        if (isset($session)) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
            $product = $repository->findOneBy(['login' => $session]);
            if ($product != null) {
                $em = $this->getDoctrine()->getManager();
                $repos = $this->getDoctrine()->getRepository('AppBundle:Friends');
                $friends = $repos->findOneBy(
                    array('idAuth' => $product->getId(), 'idDest' => $id));
                $em->remove($friends);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success',
                    'Ce bonobo a été supprimer de votre liste d\'amis.');
                return $this->redirect('/friends');
            }
            else {
                $this->get('session')->getFlashBag()->add('error',
                    'Vous devez etre connecté pour accedez a cette page.');
                return $this->redirect('/');
            }
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Vous devez etre connecté pour accedez a cette page.');
            return $this->redirect('/');
        }
    }
}
