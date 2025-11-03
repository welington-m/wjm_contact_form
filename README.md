# 🧩 WJM Contact Form Plugin

## ✍️ Autor
Welington Jose Miyazato
🔗 WJM Digital
🎓 Zend Certified Engineer | Fullstack DevOps | Digital Entrepreneur
📍 Japão 🇯🇵

Plugin robusto de formulário de contato para WordPress, com arquitetura moderna (DDD, MVC, Clean Architecture), suporte a múltiplos formulários personalizados, mensagens armazenadas no banco de dados, e interface amigável baseada em Bootstrap.

---

## 🚀 Funcionalidades

- ✅ Criação de múltiplos formulários com campos dinâmicos
- ✅ Suporte a: text, textarea, email, checkbox, radio, select, file, date, datetime-local
- ✅ Validação de campos obrigatórios
- ✅ Customização de mensagens de erro/sucesso e texto do botão
- ✅ Estilo visual baseado no Bootstrap
- ✅ Shortcode automático para embutir formulários
- ✅ Listagem de formulários com ações (editar, excluir, copiar shortcode)
- ✅ Armazenamento das mensagens no banco de dados
- ✅ Visualização de mensagens recebidas com:
  - Paginação
  - Filtro por campo (ex: nome, data)
  - Exportação CSV/Excel
  - Log de visualização

---

## 🧠 Arquitetura

- 🧩 **DDD + MVC + Clean Architecture + SRA**
- 📦 Camadas separadas: Domain, Application, Infra, Views
- 🧪 Testes com PHPUnit (Unit + Integração)
- 🔄 Injeção de dependência
- 🗂️ DTOs (Data Transfer Objects) para casos de uso
- 🧱 Repositórios e interfaces desacopladas

---

## 📁 Estrutura de Diretórios

```bash
.
├── assets/
│   ├── css/
│   ├── img/
│   └── js/
├── src/
│   ├── Application/
│   │   ├── Controllers/
│   │   ├── Mappers/
│   │   └── UseCase/
│   │       └── Form/
│   │           └── DTO/
│   ├── Domain/
│   │   ├── Entities/
│   │   ├── Factories/
│   │   └── Repositories/
│   └── Infra/
│       ├── Database/
│       ├── Hooks/
│       ├── Repositories/
│       ├── Services/
│       └── WordPress/
│           └── Helpers/
├── tests/
│   └── Unit/
│       ├── Application/
│       │   ├── Controllers/
│       │   └── UseCase/
│       │       └── Form/
│       ├── Domain/
│       │   └── Factories/
│       └── Infra/
│           └── WordPress/
│               └── Helpers/
├── views/
│   ├── admin/
│   │   ├── forms/
│   │   ├── messages/
│   │   └── parts/
│   ├── email/
│   └── public/
└── wjm-contact-form.php
```

