<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class EncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure the database connections based on environment variables
        $this->configureDatabaseEncryption();
    }

    /**
     * Configure database encryption settings.
     */
    private function configureDatabaseEncryption(): void
    {
        // Configure SQL Server with local certificate store
        if (!empty(env('CERT_PASSWORD'))) {
            Config::set('database.connections.sqlsrv.encrypt', true);
            Config::set('database.connections.sqlsrv.trust_server_certificate', true);
            Config::set('database.connections.sqlsrv.column_encryption', 'Enabled');
            Config::set('database.connections.sqlsrv.key_store_provider', 'MSSQL_CERTIFICATE_STORE');
        }

        // Configure SQL Server with Azure Key Vault
        if (!empty(env('AZURE_CLIENT_ID')) && !empty(env('AZURE_CLIENT_SECRET')) && !empty(env('AZURE_KEY_VAULT_URL'))) {
            Config::set('database.connections.sqlsrv_akv.encrypt', true);
            Config::set('database.connections.sqlsrv_akv.trust_server_certificate', true);
            Config::set('database.connections.sqlsrv_akv.column_encryption', 'Enabled');
            Config::set('database.connections.sqlsrv_akv.key_store_provider', 'AZURE_KEY_VAULT');
            Config::set('database.connections.sqlsrv_akv.key_store_authentication', 'KeyVaultClientSecret');
            Config::set('database.connections.sqlsrv_akv.key_store_principal_id', env('AZURE_CLIENT_ID'));
            Config::set('database.connections.sqlsrv_akv.key_store_secret', env('AZURE_CLIENT_SECRET'));
            Config::set('database.connections.sqlsrv_akv.key_store_location', env('AZURE_KEY_VAULT_URL'));
        }
    }
}
