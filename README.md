# google-vision-php-integration
Repositorio criado para testar a integração google vision (OCR) com php

- teste usando cloud-vision: executar server/read_text.php
- teste usando apenas api: executar server/testing-api.php

## Passos para utilizacao de cloud-vision
- ```composer require google/cloud-vision```
  - instalação da biblioteca de integração

- [instalação de CLI gcloud](https://cloud.google.com/sdk/docs/install?hl=pt-br)
  - Instalação e configuração para sincronizar permissao com o uso do serviço

- ```gcloud auth application-default login```
  - Criar arquivo de credenciais

- Parecer do teste: necessita faturamento para utilizar a api 

## Resolução de conflitos
- [erro cURL 60: certificado SSL prblm](https://stackoverflow.com/questions/35638497/curl-error-60-ssl-certificate-prblm-unable-to-get-local-issuer-certificate)
  - Esse erroa conteceu em meu ambiente windows e esses foram os passos para sua resolução
  - Baixar o arquivo .pem do [Link](https://curl.haxx.se/docs/caextract.html)
  - Colocar dentro de $caminho_php\extras\ssl\cacert.pem
  - Adicionar ao php.ini a seguinte linha: $caminho_php\extras\ssl\cacert.pem
  - Reiniciar servidor web/Apache