# PromptForge

Biblioteca versionada de **prompts**, **instrucciones** y **plantillas** con **pruebas**, **evaluaciones** y exportación **Markdown / JSON**.

Stack alineado a ResearchForge / SkillGraph: **PHP 8 + MySQL + HTML/CSS/JS** sin frameworks.

## Requisitos

- PHP 8.1+
- MySQL 5.7+ / 8.x
- Extensiones: `pdo_mysql`, `mbstring`

## Instalación

1. Copiá `.env.example` a `.env` y ajustá credenciales.
2. Creá la base (opción A o B):

```bash
# Opción A — instalador web
# Abrí http://192.168.100.50/promptforge/install.php
```

```sql
-- Opción B — manual
CREATE DATABASE promptforge CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Serví la carpeta con Apache/nginx o:

```bash
php -S localhost:8080
```

4. Abrí la app, **registrate** y empezá a forjar prompts.

## Paleta

- Tinta `#0B1520`
- Niebla `#E8F0F4`
- Cian forja `#0E9AA7`
- Ember `#E07A2F`
- Acero `#3E5C76`
- Pass `#1F7A5C`
- Fail `#C23B3B`

## MVP incluido

- Login / registro / logout (sesiones + CSRF + rate limit)
- Bibliotecas para organizar
- Prompts, instrucciones y plantillas versionados
- Detección de variables `{{nombre}}`
- Pruebas (input / esperado / criterios)
- Evaluaciones (veredicto, score, modelo, notas)
- Export MD / JSON
- Búsqueda global, tema claro/oscuro, configuración de perfil
