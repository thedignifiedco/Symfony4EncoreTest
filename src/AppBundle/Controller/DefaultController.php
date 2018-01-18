<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Enquiry;
use AppBundle\Entity\NewsletterSignup;
use AppBundle\Form\EnquiryType;
use AppBundle\Form\NewsletterSignupType;

/**
 * Class DefaultController
 */
class DefaultController extends Controller {
	/**
	 * @Route("/", name="homepage")
	 * @Template()
	 *
	 * @param Request $request
	 *
	 * @return array|Response
	 */
	public function indexAction( Request $request ) {
		$newsletterSignup = new NewsletterSignup();
		$form             = $this->createNewsletterSignupForm( $newsletterSignup );

		return $this->render( '@App/Default/index.html.twig',
			[ 'form' => $form->createView(), ]);
	}

	/**
	 * Index action
	 *
	 * @Route("/", name="submit")
	 * @Method("POST")
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse|array
	 */
	public function submitAction( Request $request ) {
		$newsletterSignup = new NewsletterSignup();
		$form             = $this->createNewsletterSignupForm( $newsletterSignup );

		$form->handleRequest( $request );
		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $newsletterSignup );
			$em->flush();
			$parameters = [
				'status'  => 'success',
				'message' => 'Thanks for signing up!',
			];
		} else {
			$parameters = [
				'status'  => 'error',
				'message' => 'This email has been used!',
			];
		}

		return new JsonResponse( $parameters );
	}

	private function createNewsletterSignupForm( NewsletterSignup $newsletterSignup ) {
		$form = $this->createForm( NewsletterSignupType::class, $newsletterSignup, [
			'action' => $this->generateUrl( 'submit' ),
			'method' => "POST",
			'attr'   => [ 'id' => 'newsletter-signup-form' ],
		] );
		$form->add( 'submit', SubmitType::class, [ 'label' => "Notify me!" ] );

		return $form;

	}


	/**
	 * @Route("/about", name="About")
	 * @Template()
	 * @param Request $request
	 *
	 * @return array|Response
	 */
	public function aboutAction() {

		return $this->render( '@App/Default/about.html.twig' );
	}

	/**
	 * Contact action
	 *
	 * @Route("/contact", name="Contact")
	 * @Template()
	 *
	 * @Method("GET")
	 *
	 * @return Response|array
	 */
	public function contactAction() {
		$enquiry = new Enquiry();
		$form    = $this->createEnquiryForm( $enquiry );

		return $this->render( '@App/Default/contact.html.twig',
			[ 'form' => $form->createView(), ]);
	}


	/**
	 * Contact action
	 *
	 * @Route("/contact", name="enquirysubmit")
	 * @Method("POST")
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse|array
	 */
	public function enquirysubmitAction( Request $request ) {
		$enquiry = new Enquiry();
		$form    = $this->createEnquiryForm( $enquiry );

		$form->handleRequest( $request );
		if ( $form->isValid() ) {
			$message = \Swift_Message::newInstance()
			                         ->setSubject( $enquiry->getSubject() )
			                         ->setFrom( $enquiry->getEmail() )
			                         ->setTo( 'dignified.sorinolu-bimpe@squaremile.com' )
			                         ->setBody( $enquiry->getUsermessage() );

			$result = $this->get( 'mailer.service' )->send( $message );

			$parameters = [
				'status'  => 'success',
				'message' => 'Thanks for your enquiry!',
				'result'  => $result,
			];
		} else {
			$errors  = $form->getErrors();
			$message = 'Your message failed!';
			foreach ( $errors as $error ) {
				$message .= "\n" . $error->getMessage();
			}
			$parameters = [
				'status'  => 'error',
				'message' => $message,
			];
		};

		return new JsonResponse( $parameters );
	}

	private function createEnquiryForm( Enquiry $enquiry ) {
		$form = $this->createForm( EnquiryType::class, $enquiry, [
			'action' => $this->generateUrl( 'enquirysubmit' ),
			'method' => "POST",
			'attr'   => [ 'id' => 'enquiry-form' ],
		] );
		$form->add( 'submit', SubmitType::class, [ 'label' => "Send" ] );

		return $form;

	}

	/**
	 * @Route("/brands", name="Brands")
	 * @Template()
	 *
	 * @return array|Response
	 */
	public function brandsAction() {

		return $this->render( '@App/Default/brands.html.twig' );
	}
}