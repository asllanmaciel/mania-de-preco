# Avatar customizavel no perfil

**tipo:** feature  
**impacto:** medio  
**modulo:** perfil / UX / UI

## Resumo executivo

O perfil do usuario passou a aceitar foto/avatar customizavel, permitindo que a topbar exiba uma imagem real quando configurada e mantenha iniciais como fallback quando ainda nao houver foto.

## Entregas realizadas

- criacao do campo `avatar_path` na tabela de usuarios
- inclusao do avatar no model de usuario com URL publica calculada
- suporte a upload de imagem no perfil administrativo
- validacao de formatos JPG, PNG e WebP com limite de 2 MB
- remocao automatica do avatar anterior ao enviar uma nova foto
- exibicao da foto no perfil do usuario
- exibicao da foto na topbar do painel lojista
- exibicao da foto no menu de perfil do backoffice
- fallback por iniciais quando nao houver avatar cadastrado
- cobertura automatizada para upload e persistencia do avatar

## Estrategia aplicada

A estrategia foi evoluir a personalizacao visual sem adicionar uma camada complexa de storage. As imagens sao salvas em uma pasta publica do projeto para funcionar bem no ambiente Docker atual e no navegador local, mantendo a implementacao simples para esta fase.

## Resultado

O sistema ganha mais acabamento de produto e uma topbar mais humana, especialmente para uso diario por lojistas, operadores e super admins. A base tambem prepara proximos passos como recorte de imagem, remocao manual do avatar e preferencias individuais de interface.
