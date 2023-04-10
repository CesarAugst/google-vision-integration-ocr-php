<?php
//token de autenticacao da api
$token = 'api_key';
//verifica se o arquivo e pdf
if(strpos($_GET['url'], '.pdf')){
    //define url da requisicao
    $url = "https://vision.googleapis.com/v1/files:annotate";
    //conversao da imagem
    $pdf_base64 = convert_img_file_in_base64($_GET['url']);
    //exibe o resultado da requisicao de conversar de pdf em texto
    echo request_text_in_pdf($url, $token, $pdf_base64);
}else{
    //define url da requisicao
    $url = "https://vision.googleapis.com/v1/images:annotate";
    //conversao da imagem
    $img_base64 = convert_img_file_in_base64($_GET['url']);
    //exibe o sultado da requisicao de conversao de imagem em texto
    echo request_text_in_image($url, $token, $img_base64);
}



/*Funcoes*/

//desc: realiza requisicao para ober resultado em texto de pdf
//params: (string) url da requisicao, (string) token da requisicao, (string) url do documento
//return: (obj) resposta da requisicao
function request_text_in_pdf($url, $token, $pdf_base64){
    //junta url e token como parametro
    $full_url = "$url?key=$token";
    //inicia requisicao
    $ch = curl_init();
    //define a url para requisicao
    curl_setopt($ch, CURLOPT_URL, $full_url);
    //armazenar resposta em variavel
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //requisicao post
    curl_setopt($ch, CURLOPT_POST, 1);
    //corpo da requisicao
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{'requests':[{'inputConfig':{'content': '$pdf_base64','mimeType':'application/pdf'},'features':[{'type':'DOCUMENT_TEXT_DETECTION'}]}]}");
    //array de cabeccalhos
    $headers = array();
    //aceita resposta como json
    $headers[] = 'Accept: application/json';
    //conteudo da requisicao como json
    $headers[] = 'Content-Type: application/json';
    //implementa os cabecalhos
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //pega o resultado da requisicao
    $result = curl_exec($ch);
    //se houve erro na requisicao
    if (curl_errno($ch)) {
        //exibe o erro
        echo 'Error:' . curl_error($ch);
    }
    //finaliza a requisicao
    curl_close($ch);
    //retorna o resultado da requisicao
    return $result;
}

//desc: realzia requisicao apra obter resultado em texto de imagem
//params: (string) url da requisicao, (string) token, (string) imagem em base64
//retur: (obj) resposta da requisicao convertida em json
function request_text_in_image($url, $token, $img_base64){
    //junta url e token como parametro
    $full_url = "$url?key=$token";
    //inicia requisicao
    $ch = curl_init();
    //define a url para requisicao
    curl_setopt($ch, CURLOPT_URL, $full_url);
    //armazenar resposta em variavel
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //requisicao post
    curl_setopt($ch, CURLOPT_POST, 1);
    //corpo da requisicao
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{'requests': [{'image': {'content': '$img_base64'},'features': [{'type': 'TEXT_DETECTION'}]}]}");
    //array de cabeccalhos
    $headers = array();
    //aceita resposta como json
    $headers[] = 'Accept: application/json';
    //conteudo da requisicao como json
    $headers[] = 'Content-Type: application/json';
    //implementa os cabecalhos
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //pega o resultado da requisicao
    $result = curl_exec($ch);
    //se houve erro na requisicao
    if (curl_errno($ch)) {
        //exibe o erro
        echo 'Error:' . curl_error($ch);
    }
    //finaliza a requisicao
    curl_close($ch);
    //retorna o resultado da requisicao
    return $result;
}

//desc: converte uma imagem em base64
//params: (string) caminho da imagem
//return: (string) imagem convertida em base64
function convert_img_file_in_base64($image_path){
    //requisita a imagem
    $img = file_get_contents($image_path);
    //retorna conversao para base64
    return base64_encode($img);
}
