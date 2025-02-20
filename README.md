# TaskCheck

TaskCheck é um sistema de gestão de atividades complementares desenvolvido para atender às necessidades da PUCPR. O projeto foi criado na disciplina **"Experiência Criativa: Projetando Soluções Computacionais"** durante o terceiro período do curso de **Engenharia de Software** da **PUC-PR**.

## Sobre o Projeto
TaskCheck tem como objetivo facilitar o gerenciamento de atividades complementares, ajudando alunos a monitorar o progresso de suas horas, permitindo que professores validem certificados de forma eficiente e proporcionando aos coordenadores um controle centralizado das categorias de atividades.

## Funcionalidades
### Alunos
- Submissão de relatórios de atividades com anexo de certificados.
- Visualização do status de submissões (“Aguardando validação”, “Válido”, “Inválido”).
- Edição de relatórios enquanto aguardam validação.
- Recebimento de feedback estruturado sobre submissões.
- Acompanhamento da progressão de horas validadas de cada categoria.

### Professores
- Visualização e validação de relatórios de atividades.
- Revisão e inclusão de feedback para alunos.
- Consulta de certificados anexados para verificação da autenticidade.

### Coordenadores
- Gerenciamento de categorias de atividades complementares.
- Controle sobre os limites de carga horária de cada categoria.
- Remoção de categorias desatualizadas com opção de recategorizar atividades pendentes.

## Sistema de Rotas
O TaskCheck utiliza um sistema de rotas estruturado para organizar a navegação entre diferentes funcionalidades do sistema. A estrutura segue o padrão:

```
index.php -> router.php -> controllers -> views
```

### Exemplo de fluxo de rota:
- **index.php**: ponto de entrada que carrega as funções e o roteador.
- **router.php**: analisa a URL solicitada e redireciona para o controlador adequado.
- **controllers/aluno.php**: controlador que gerencia a lógica da página do aluno.
- **views/partials + views/aluno.view.php**: renderização da página com os elementos de layout e conteúdo.

#### Estrutura de arquivos relacionados:
```
index.php
|-- router.php
|-- controllers/
|   |-- aluno.php
|-- views/
    |-- partials/
    |   |-- head.php
    |   |-- header.php
    |   |-- sidebar.php
    |   |-- footer.php
    |-- aluno.view.php
```

## Tecnologias Utilizadas

![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/bootstrap-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Apache](https://img.shields.io/badge/apache-%23D42029.svg?style=for-the-badge&logo=apache&logoColor=white)

## Como Executar
1. Clone o repositório:
   ```bash
   git clone https://github.com/gustgaspar/Taskcheck.git
   ```
2. Configure o ambiente com Apache, PHP e MySQL. Utilize ferramentas como **XAMPP**, **MAMP**, **Laragon** ou outras semelhantes para facilitar a configuração do ambiente.
3. Importe o banco de dados incluído. (`database/database.php` caso queira alterar informações de conexão do banco de dados)
4. Verifique se o arquivo `httpd.conf` do Apache está configurado com `AllowOverride All` para permitir que o `.htaccess` funcione corretamente.
5. Assegure-se de que a linha `LoadModule rewrite_module modules/mod_rewrite.so` não esteja comentada no `httpd.conf`. Se estiver, remova o `#` para habilitar o módulo.
6. Faça ajustes no arquivo `.htaccess` do projeto, se necessário, para garantir o funcionamento do sistema de rotas em outros ambientes.
7. Execute o servidor local e acesse `localhost` (ou o endereço correspondente ao seu ambiente).
8. Como o projeto foi pensado para importar (hipoteticamente) usuários de bancos de dados externos de uma instituição de ensino, não foi criada a função de cadastro de novos usuários. Para adicionar um usuário, acesse o painel do administrador em `localhost/admin` e crie o usuário com as informações e o tipo desejado.

---
# TaskcheckOrgao
# TaskcheckOrgao
