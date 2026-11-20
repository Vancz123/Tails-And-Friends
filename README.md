# Tails & Friends

CRM em Laravel para consultar e manter uma coleção de personagens e jogos do universo Sonic. O conteúdo é público para consulta; o registo e login usam Laravel Fortify.

As páginas partilham o layout master em `resources/views/layouts/master.blade.php`, com uma paleta inspirada no Tails (laranja, dourado e creme). As listas incluem pesquisa por texto; a lista de jogos também permite filtrar por ano.

Na ficha de cada personagem, o jogo de estreia pode ser escolhido na edição. A ficha destaca a imagem e o título desse jogo e lista os restantes jogos apenas pelos nomes.

## Iniciar localmente

```bash
composer install
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

O seeder cria personagens e jogos de demonstração e a conta `test@example.com` com a palavra-passe `password` (apenas para desenvolvimento local).

## Perfis

- Utilizadores não autenticados podem consultar personagens e jogos.
- Utilizadores autenticados podem editar personagens, jogos e as relações entre eles.
- Administradores também podem criar e apagar registos.

Para promover uma conta registada a administradora, altere manualmente o campo `role` para `admin` na tabela `users`. Por exemplo, com SQLite:

```sql
UPDATE users SET role = 'admin' WHERE email = 'email-registado@example.com';
```

Novos registos recebem sempre o perfil `user`; o formulário de registo não permite atribuir privilégios de administrador.

## Imagens

As imagens carregadas pelos formulários são validadas (JPG, PNG ou WEBP, até 2 MB) e guardadas no disco `public` do Laravel, em `storage/app/public/characters` ou `storage/app/public/games`. Para as servir através de `public/storage`, execute uma vez:

```bash
php artisan storage:link
```

O caminho relativo fica guardado em `image_path`; imagens antigas definidas por URL continuam a ser apresentadas como fallback. As imagens dos registos de demonstração são placeholders; substitua-as por imagens autorizadas quando necessário.
