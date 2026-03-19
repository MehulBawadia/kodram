@servers(['web' => 'username@ip -p portNumber'])

@task('deploy')
    cd /home/username/domains/kodram
    echo "Inside kodram directory..."

    rm -rf vendor/
    echo "Removed vendor/ directory"

    git reset --hard origin/main
    echo "Removed any untracked files and/or folders"

    git pull origin main

    composer2 install --optimize-autoloader --no-dev

    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    export NVM_DIR="$([ -z "${XDG_CONFIG_HOME-}" ] && printf %s "${HOME}/.nvm" || printf %s "${XDG_CONFIG_HOME}/nvm")"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
    nvm use 25

    npm install

    export RAYON_NUM_THREADS=1
    export UV_THREADPOOL_SIZE=1

    npm run build

    rm -rf node_modules/
    echo "Removed node_modules/ directory.."

    echo "Check https://kodram.bmehul.com"
@endtask
