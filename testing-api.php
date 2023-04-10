<?php
//registra o tempo inicial
$initialTime = time();
//token de autenticacao da api
$token = 'api_key';
//bool para ser pdf
$is_pdf = strpos($_GET['url'], '.pdf');
//verifica se o arquivo e pdf
if($is_pdf){
    //define url da requisicao
    $url = "https://vision.googleapis.com/v1/files:annotate";
    //conversao da imagem
    $pdf_base64 = convert_img_file_in_base64($_GET['url']);
    //exibe o resultado da requisicao de conversar de pdf em texto
    $result_translate = request_text_in_pdf($url, $token, $pdf_base64);
    //converte a resposta em um array dividido por linhas
    $array_result_per_lines = format_result_per_line(json_decode($result_translate), $is_pdf);
}else{
    //define url da requisicao
    $url = "https://vision.googleapis.com/v1/images:annotate";
    //conversao da imagem
    $img_base64 = convert_img_file_in_base64($_GET['url']);
    //exibe o sultado da requisicao de conversao de imagem em texto
    $result_translate = request_text_in_image($url, $token, $img_base64);
    //converte a resposta em um array dividido por linhas
    $array_result_per_lines = format_result_per_line(json_decode($result_translate), $is_pdf);
}

//armazena comparativo
store_comparative_result($initialTime, $_GET['url'], $array_result_per_lines);

echo $result_translate;

/*Funcoes*/


//desc: pega a resposta da conversao e formata em um array dividido por linhas
//params: (obj) resposta da requisicao
//return: (array) conversao em array da resposta
function format_result_per_line($result_translate, $is_pdf)
{
    //se for um pdf
    if($is_pdf){
        //pega o array onde esta o texto da pagina
        $text_page = $result_translate->responses[0]->responses[0]->fullTextAnnotation->text;
    }else{
        //pega o array onde esta o texto da pagina
        $text_page = $result_translate->responses[0]->textAnnotations[0]->description;
    }


    //retorna resultado quebrado por linhas
    return implode(PHP_EOL, preg_split('/\n/', $text_page));
}

//desc: gerencia o armazenamento do resultado para fins de comparacaao
//parasm: (time) tempo inicial, (string) url, (string) resultado da conversao
//return: nenhum
function store_comparative_result($initialTime, $url, $result_translate){
    //caminho para salvar resultados
    $path = "./results";
    //nome do arquivo
    $file_name = "google-api-" . str_replace(".", "-", substr($url, -5)) . ".txt";
    //se o caminho do diretorio nao existir
    if(!is_dir($path)){
        //cria o diretorio
        mkdir($path, 0755);
    }

    //junta o tempo gasto com o resultado da conversao
    $array_data = array_merge(
        ['tempo_gasto' =>'tempo gasto: '.(time() - $initialTime).' segundos'],
        ['url' => "url: $url"],
        ['resultado' => "resultado: \n$result_translate"]
    );

    //salva em arquivo
    file_put_contents("$path/$file_name", implode( PHP_EOL, ($array_data))); //armazena resultado

}

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
