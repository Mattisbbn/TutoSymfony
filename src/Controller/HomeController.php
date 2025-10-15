<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

final class HomeController extends AbstractController{


    #[Route(path:"/", name:"home")]
    function index (Request $request): Response{
        return new Response("Bonjour les gens" . $request->query->get("name"));
    }

}
  

