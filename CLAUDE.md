# QuietTime (quitetime.ru) — заметки для агента

Ежедневник/девоционал. PHP, **Yii2** (`common` / `backend` / `frontend` / `console`), PHP 5.6.
Публичный сайт: `https://quitetime.ru`. Админка: `https://quitetime.ru/panel` (backend-модуль, раздел «Задачи» = ежедневный контент).

## Окружения

| | Путь | Git remote | Роль |
|---|---|---|---|
| **Прод** | Beget, `kidivaf9_1@kidivaf9.beget.tech:~/quitetime.ru/public_html` | `origin` через **read-only deploy key** (`~/.ssh/id_ed25519_deploy` на сервере) | Живой сайт. Никогда не редактировать файлы через SSH напрямую — только `git pull`. |
| **Локально** | `~/Projects/quiettime` (эта машина) | `origin` через личный SSH-ключ (`~/.ssh/id_ed25519_quiettime`) | Разработка и тестирование в Docker, полная копия прод-данных. |
| **GitHub** | [github.com/kimdinvibe/quiettime](https://github.com/kimdinvibe/quiettime) | private | Источник истины. `main` = должно быть безопасно раскатить на прод. |

Бэкапы: `~/quitetime.ru/backups/` на сервере (файлы) + дампы БД (см. ниже).

## Правила для агента (обязательно к соблюдению)

1. **Никогда не редактировать файлы на проде напрямую через SSH.** Любое изменение кода идёт через: правка в `~/Projects/quiettime` → тест в локальном Docker → коммит → пуш в `main` на GitHub → `git pull` на проде. Прямое редактирование через SSH допустимо только как экстренный hotfix, и после — тот же патч обязательно должен быть закоммичен в git (иначе следующий `git pull` его перезатрёт или создаст конфликт).
2. **Прод может только `git pull`, никогда `git push`.** Deploy-ключ на сервере — read-only по дизайну. Если возникает соблазн запушить с прода — это сигнал, что что-то в процессе пошло не так.
3. **`git pull` на проде настроен как `pull.ff-only`.** Если pull не проходит fast-forward — значит на проде есть расхождение с историей (кто-то правил файлы вручную). Не форсить (`reset --hard`) не разобравшись — сначала посмотреть `git status`/`git diff`, при необходимости сделать бэкап расходящихся файлов.
4. **Перед любой миграцией БД или потенциально разрушительной операцией на проде — свежий `mysqldump` бэкап.** Хранить с датой в имени, вне веб-корня (`~/quitetime.ru/backups/` или аналог), никогда не в `public_html` (публично доступно) и никогда не в git.
5. **Не коммитить**: `.env`, `vendor/`, `*/runtime/`, `storage/web/`, `storage/cache/`, дампы БД (`*.sql`, `*.sql.gz`) — всё это уже в `.gitignore`, не пытаться туда что-то из этого добавить силой (`git add -f`).
6. **`vendor/` не версионируется и `composer install` на этом проекте сейчас сломан** — один из транзитивных dev-зависимостей (`finalbytes/google-distance-matrix-api`) удалён из GitHub, установка с нуля падает. Практическое следствие: при добавлении/обновлении composer-пакета руками синхронизировать `vendor/` между локалью и продом через `rsync` (а не полагаться на `composer install` на сервере). Почини эту зависимость (заменить/убрать пакет) — отдельная задача, если до неё дойдут руки.
7. **Локальный Docker специально держит те же версии, что и прод** (PHP 5.6, MySQL 5.6) — не апгрейдить их походя ради удобства, это база на которой мы удостоверяемся, что "работает локально" значит "работает на проде". Если апгрейд неизбежен (например, из-за EOL архивов Debian) — обсуждать с пользователем перед изменением.
8. **Тестировать в Docker перед пушем в `main`.** `main` считается "готовым к деплою" по умолчанию — не пушить туда заведомо сломанный/непроверенный код.
9. Для нетривиальных изменений — отдельная ветка (`feature/...`), не коммитить напрямую в `main`. Для мелких безопасных правок (тайпо, конфиги) можно и напрямую.
10. При изменении схемы БД — предпочитать Yii-миграции (`console/yii migrate/create ...`, `console/yii migrate`) вместо ручных `ALTER TABLE`, и прогонять их и локально, и на проде (после бэкапа).

## Как вносить изменения — пошагово

### 1. Подготовка
```bash
cd ~/Projects/quiettime
docker compose -f docker-compose.local.yml up -d   # если ещё не поднято
git checkout main && git pull                       # актуализировать main
git checkout -b feature/my-change
```

### 2. Правки и тест локально
- Редактировать код как обычно.
- Если менялись composer-зависимости: `docker compose -f docker-compose.local.yml exec app composer install` (может не сработать из-за пункта 6 правил — тогда руками добавить нужные файлы в `vendor/`).
- Если нужна миграция БД: `docker compose -f docker-compose.local.yml exec app console/yii migrate/create <name>`, написать миграцию, применить: `docker compose -f docker-compose.local.yml exec app console/yii migrate`.
- Проверить в браузере: http://localhost:8000 (фронт), http://localhost:8000/panel (админка, логин — учётка `manager`).
- Если трогали API (`task/save`, `task/find` и т.п.) — проверить curl-запросом или через саму форму в `/panel`.

### 3. Коммит и пуш
```bash
git add <файлы>
git commit -m "..."
git push -u origin feature/my-change
```
Можно смёржить сразу в `main` (`git checkout main && git merge feature/my-change && git push`), либо открыть PR на GitHub для ревью — по обстоятельствам.

### 4. Деплой на прод
```bash
ssh kidivaf9_1@kidivaf9.beget.tech
cd ~/quitetime.ru/public_html
git pull                     # подтянет main; из-за ff-only упадёт, если на проде ручные правки — не форсить, разбираться
```
- Если были новые composer-зависимости — синхронизировать `vendor/` (`rsync` с локали на прод).
- Если были миграции БД — **сначала бэкап** (`mysqldump`), потом `console/yii migrate` на проде.
- Проверить `curl -I https://quitetime.ru/` и `https://quitetime.ru/panel` — коды ответа как обычно (302 на редиректы — норма).

### 5. Если что-то сломалось
- Откат кода: на проде `git log --oneline`, затем `git checkout <предыдущий_коммит> -- .` (точечно) или (с осторожностью) `git reset --hard <предыдущий_коммит>`, предварительно свежий `git status`/бэкап.
- Откат БД: восстановить из последнего `mysqldump`-бэкапа перед миграцией.

## Структура проекта, поля Task, API

(см. таблицы соответствия полей формы/модели `Task`, список эндпоинтов `TaskController` и логику выбора стихов Библии — зафиксированы отдельно, актуальны на 2026-07-04; при существенных изменениях кода — обновить этот раздел)

- `common/models/` — модели: `Task`, `Bible`, `TaskVerse`, `TaskApplication`, `User`
- `backend/controllers/TaskController.php` — CRUD + JSON API раздела «Задачи» (`find`, `items`, `save`, `delete`, `chapters`, `verses`)
- `backend/web/js/calendar.js` — клиентская логика: календарь, подбор стихов, AJAX save/delete
- Авторизация API: сессионная (cookie), CSRF отключён (`enableCsrfValidation => false` в `backend/config/web.php`), доступ к `task` контроллеру — роль `manager`.
