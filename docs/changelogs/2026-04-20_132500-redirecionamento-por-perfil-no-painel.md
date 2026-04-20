# Redirecionamento por perfil no painel

## Contexto
- Ao acessar uma área de painel incompatível com o perfil logado, o sistema retornava 403 diretamente.
- Isso podia acontecer quando um super admin acessava `/admin`, que é a área do lojista.
- Para uso local e lançamento, a experiência precisa orientar o usuário para a área correta sem parecer erro de login.

## Alterações realizadas
- Ajustado o middleware de painel para redirecionar requisições `GET` ao painel inicial correto do usuário.
- Mantido 403 para ações sensíveis de perfis incorretos, como `POST`, `PATCH`, `PUT` e `DELETE`.
- Atualizados testes para cobrir redirecionamento amigável e bloqueio de ações indevidas.

## Como validar
- Super admin em `/admin` deve ir para `/super-admin`.
- Lojista em `/super-admin` deve ir para `/admin`.
- Cliente em `/admin` deve ir para `/cliente`.
- Ações administrativas em painel errado continuam bloqueadas.
