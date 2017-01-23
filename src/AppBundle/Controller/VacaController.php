<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Buzz\Browser;
use AppBundle\Entity\Cow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class VacaController extends Controller
{
    /**
     * @Route("/", name="cow_index")
     */
    public function indexAction(Request $request)
    {
    	$cows = array();
    	$return = array();

    	try {
    		$offset = 0;
	    	$limit = 100;

	    	do {
	    		$browser = new Browser();
		    	$headers = array(
				'Content-Type' => 'application/json',
				'access-token' => $this->container->getParameter('tag_access_token')
			);

	    		$url = implode('/',[$this->container->getParameter('tag_url'),'cows?limit='.$limit.'&offset='.$offset]);
			$response = $browser->get($url, $headers);
		    	$content = json_decode($response->getContent());
		    	
	    		
	    		if ( !is_array($content) && property_exists($content, 'error') ){

		    	} else {
		    		$return = array_merge($return,$content);
		    	}

		    	$offset += $limit;
	    	} while ( is_array($content) && sizeof($content) == $limit);

	    	$menor_custo = null;
	    	$id_vaca_menor_custo = null;

	    	foreach( $return as $k => $c){
	    		$cow = new Cow($c);
	    		
	    		if ( (is_null($menor_custo) && is_null($id_vaca_menor_custo) || ($menor_custo > $cow->custoMedioAnual())) ){
	    			$menor_custo = $cow->custoMedioAnual();
	    			$id_vaca_menor_custo = $cow->getId();
	    		}

	    		array_push($cows, $cow);
	    	}

		return $this->render('vaca/index.html.twig', array(
		    'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
		    'cows' => $cows,
		    'id_vaca_menor_custo' => $id_vaca_menor_custo
		));
    	} catch (Exception $e) {
    		//var_dump($e);
    	}
    }

    /**
     * @Route("/novo", name="cow_create")
     */
    public function createAction(Request $request)
    {
    	try {
    		$cow = new Cow;

	    	$form = $this->createFormBuilder($cow,array(
	    		'attr' => array(
    				'id' => 'form-cow',
    				'novalidate'=> 'novalidate'
    			)
	    		))->add('weight', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
		            		'label' => 'Peso', 
		            		'required' => true,
		            		'attr' => array(
		            			'class'=>'decimal2'
	            			)
            			))
		            ->add('age', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
		            		'label' => 'Idade', 
		            		'required' => true,
		            		'attr' => array(
		            			'class'=>'inteiro'
	            			)
	            		))
		            ->add('price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
		            		'label' => 'Preço', 
		            		'required' => true, 
		            		'currency' => 'BRL',
		            		'attr' => array(
		            			'class' => 'decimal2'
	            			)
	            		))
		            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
		            		'label' => 'Salvar',
		            		'attr' => array(
		            			'class' => 'btn-block btn-primary'
	            			)
	            		))
		            ->getForm();

		$form->handleRequest($request);

		if ( $form->isSubmitted() && $form->isValid() ) {
			$browser = new Browser();
		    	$headers = array(
				'Content-Type' => 'application/json',
				'access-token' => $this->container->getParameter('tag_access_token')
			);

			$cow = $form->getData();

			$response = $browser->post( implode('/',[$this->container->getParameter('tag_url'),'cows']), $headers, json_encode($cow) );
		    	$content = json_decode($response->getContent());

		    	if ( property_exists($content, 'error') ){

		    	} else {

		    	}

			return $this->redirectToRoute('cow_index');
		}

    		return $this->render('vaca/create.html.twig', array(
		    'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
		    'form' => $form->createView(),
		));
    	} catch (Exception $e) {
    		
    	}
    }

    /**
     * @Route("/vaca/{id}/edit", name="cow_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        try {
        	$browser = new Browser();
    	$headers = array(
		'Content-Type' => 'application/json',
		'access-token' => $this->container->getParameter('tag_access_token')
	);

	$response = $browser->get( implode('/',[$this->container->getParameter('tag_url'),'cows',$id]), $headers );
    	$cow = json_decode($response->getContent());

    	if ( property_exists($cow, 'error') ){

    	} else {

    	}

    	$form = $this->createFormBuilder($cow,array(
		            'attr' => array(
    				'id' => 'form-cow',
    				'novalidate'=> 'novalidate'
    			)
	    		))->add('weight', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
		            		'label' => 'Peso', 
		            		'required' => true,
		            		'attr' => array(
		            			'class'=>'decimal2'
	            			)
            			))
		            ->add('age', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
		            		'label' => 'Idade', 
		            		'required' => true,
		            		'attr' => array(
		            			'class'=>'inteiro'
	            			)
	            		))
		            ->add('price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
		            		'label' => 'Preço', 
		            		'required' => true, 
		            		'currency' => 'BRL',
		            		'attr' => array(
		            			'class' => 'decimal2'
	            			)
	            		))
		            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
		            		'label' => 'Salvar',
		            		'attr' => array(
		            			'class' => 'btn-block btn-primary'
	            			)
	            		))
		            ->setAction($this->generateUrl('cow_edit',array('id'=>$id)))
		            ->getForm();

		$form->handleRequest($request);

	if ( $form->isSubmitted() && $form->isValid() ) {
		$browser = new Browser();
	    	$headers = array(
			'Content-Type' => 'application/json',
			'access-token' => $this->container->getParameter('tag_access_token')
		);

		$cow = $form->getData();

		$response = $browser->put( implode('/',[$this->container->getParameter('tag_url'),'cows',$id]), $headers, json_encode($cow) );
	    	$content = json_decode($response->getContent());

	    	if ( property_exists($content, 'error') ){

	    	} else {

	    	}

		return $this->redirectToRoute('cow_index');
	}

    	return $this->render('vaca/create.html.twig', array(
		    'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
		    'form' => $form->createView(),
		));
        } catch (Exception $e) {
        	
        }
    }

    /**
     * @Route("/vaca/{id}/delete", name="cow_delete", requirements={"id": "\d+"})
     * @Method({"GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        try {
        	$browser = new Browser();
    	$headers = array(
		'Content-Type' => 'application/json',
		'access-token' => $this->container->getParameter('tag_access_token')
	);

	$response = $browser->delete( implode('/',[$this->container->getParameter('tag_url'),'cows',$id]), $headers );
    	$cow = json_decode($response->getContent());

    	return $this->redirectToRoute('cow_index');
        } catch (Exception $e) {
        	
        }
    }
}
