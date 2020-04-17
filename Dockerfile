FROM php:7.4-alpine3.11

# Adiciona um novo grupo
RUN addgroup -g 1000 app

# Adiciona um novo usuário
RUN adduser \
    --disabled-password \
    --shell "/bin/bash" \
    --home "/app" \
    --ingroup "app" \
    --no-create-home \
    --uid "1000" \
    "app"

# Seta o diretorio de trabalho
WORKDIR /app

# Seta o usuário padrão para app
USER app