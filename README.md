# WJM Contact Form

![License](https://img.shields.io/badge/license-GPLv2-blue.svg)  
**Autor:** Welington Jose Miyazato  
**Licença:** GPLv2 or later  
**Versão:** 1.0  
**Compatível com:** WordPress 5.8+ | PHP 7.4+  
**Status:** Gratuito e de código aberto

---

## ✉️ Plugin de Formulário de Contato para WordPress

O **WJM Contact Form** é um plugin moderno e seguro de formulário de contato, construído com princípios de Clean Architecture e Domain-Driven Design (DDD). Ideal para projetos profissionais que exigem qualidade de código, segurança e personalização completa — tudo isso sem depender de plugins inchados.

---

## 🚀 Recursos

- ✅ Campos personalizáveis via painel (nome, email, mensagem, assunto)
- ✅ Validações completas no backend com mensagens configuráveis
- ✅ Registro das mensagens no banco de dados
- ✅ Painel administrativo com busca, filtro e paginação
- ✅ Exportação de mensagens para CSV
- ✅ Exportação e restauração de configurações via JSON
- ✅ Pronto para multilíngue (personalização via painel)
- ✅ Arquitetura limpa e testável (DDD + Clean Architecture)
- ✅ Totalmente gratuito e de código aberto

---

## 📦 Instalação

1. Faça o download do plugin (ou clone este repositório)
2. Copie a pasta `wjm-contact-form/` para o diretório `wp-content/plugins/`
3. Ative o plugin via painel do WordPress
4. Vá em **Configurações > Formulário WJM** para configurar
5. Use o shortcode `[wjm_contact_form]` onde quiser exibir o formulário

---

## 🧩 Shortcode

```php
[wjm_contact_form]
```

## Diagrama
```
+------------------------------------------+
|               WordPress Core             |
|   (hooks, shortcodes, filters, etc.)     |
+---------------------+--------------------+
                      |
                      v
+------------------------------------------+
|         Interface / Presentation Layer    |
| [form-handler.php]                        |
| - Recebe $_POST, valida nonce             |
| - Chama o caso de uso                     |
| - Exibe mensagens de erro                 |
+---------------------+--------------------+
                      |
                      v
+------------------------------------------+
|         Application Layer                 |
| [UseCases/HandleContactForm.php]          |
| - Orquestra o fluxo                       |
| - Valida campos e mensagens personalizadas|
| - Chama serviços de envio ou persistência |
+---------------------+--------------------+
                      |
                      v
+------------------------------------------+
|         Domain Layer                      |
| [Entities/FormData.php]                   |
| [ValueObjects/Email.php]                  |
| - Regras de domínio                       |
| - Entidades e validações                  |
+---------------------+--------------------+
                      |
                      v
+------------------------------------------+
|         Infrastructure Layer              |
| [Services/EmailSender.php]                |
| [Repositories/FormStorage.php]            |
| - Implementação concreta de email         |
| - Armazenamento no banco ou log           |
+------------------------------------------+
```