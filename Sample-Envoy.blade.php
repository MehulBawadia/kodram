@servers(['web' => 'username@ip -p portNumber'])

@task('deploy')
    cd /home/username/domains/kodram
    echo "Inside kodram directory..."

    git fetch origin
    git reset --hard origin/main
    git clean -fd
    echo "Source code updated..."

    composer2 install --no-dev --prefer-dist --optimize-autoloader

    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "Laravel cache rebuilt..."

    echo "Check https://kodram.bmehul.com"

    {{-- export NVM_DIR="$([ -z "${XDG_CONFIG_HOME-}" ] && printf %s "${HOME}/.nvm" || printf %s "${XDG_CONFIG_HOME}/nvm")"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
    nvm use 25

    npm install

    export RAYON_NUM_THREADS=1
    export UV_THREADPOOL_SIZE=1

    npm run build

    rm -rf node_modules/
    echo "Removed node_modules/ directory.."

    echo "Check https://kodram.bmehul.com" --}}
@endtask
