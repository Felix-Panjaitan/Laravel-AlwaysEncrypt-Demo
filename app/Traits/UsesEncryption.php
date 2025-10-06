<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait UsesEncryption
{
    /**
     * Get the database connection for this model.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        // Determine which connection to use based on the encryption type
        if (property_exists($this, 'useAzureKeyVault') && $this->useAzureKeyVault) {
            return DB::connection('sqlsrv_akv');
        }

        // Use the default connection (can be local certificate store)
        return DB::connection($this->connection ?? config('database.default'));
    }

    /**
     * Use Azure Key Vault for encryption.
     *
     * @return $this
     */
    public function withAzureKeyVault()
    {
        $this->useAzureKeyVault = true;
        return $this;
    }

    /**
     * Use local certificate store for encryption.
     *
     * @return $this
     */
    public function withLocalCertStore()
    {
        $this->useAzureKeyVault = false;
        return $this;
    }
}
