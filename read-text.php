<?php
//utilizacao das bibliotecas do composer
require_once 'vendor/autoload.php';
//chama a classe do google-vision
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

//instancia a classe
$imageAnnotatorClient = new ImageAnnotatorClient();
//tentativa de requisicao
try{
    //caminho da imagem
    $image_path = 'https://i3.ytimg.com/vi/oeVPsNBTWqU/hqdefault.jpg';
    $image_path = 'assets/A2.jpg';
    //pega a imagem
    $imageContent = file_get_contents($image_path);
    //faz a deteccao de texto
    $response = $imageAnnotatorClient->textDetection($imageContent);
    //armazena o texto
    $text = $response->getTextAnnotations();
    //exibe o texto
    echo $text[0]->getDescription();
    //fecha a conexao
    $imageAnnotatorClient->close();
} catch (Exception $error){ //caso houver erro
    //exibe o erro
    echo $error->getMessage();
}