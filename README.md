1. Запутить проект

```angular2html
make dc_build
make dc_up
```

2. Зайти в контейнер 

```angular2html
make app_bash
```

3. Выполнить миграции

```angular2html
php bin/console doctrine:migrations:migrate
```

4. Установить переменные окружения в [.env](.env)

Инструкция по получения токена Яндекса
https://cloud.yandex.ru/docs/iam/operations/iam-token/create


Получите OAuth-токен в сервисе Яндекс.OAuth. 
Для этого перейдите по https://oauth.yandex.ru/authorize?response_type=token&client_id=1a6990aa636648e9b2ef855fa7bec2fb, 
нажмите Разрешить и скопируйте полученный OAuth-токен.

Для jira создать токен авторизации в личном кабинете 
```
TOKEN_YANDEX=
ORG_ID_YANDEX=645498

TOKEN_JIRA=
```

5. Загрузить задачи 

```angular2html
php -d memory_limit=4000M bin/console app:load-issues
```

6. Загрузить комментарии 

```angular2html
php -d memory_limit=4000M bin/console app:load-comment
```

7. Загруить файлы 

```angular2html
php -d memory_limit=4000M bin/console app:load-file
```

8. Выгрузка в jira

```angular2html
php -d memory_limit=4000M bin/console app:upload-issues
```