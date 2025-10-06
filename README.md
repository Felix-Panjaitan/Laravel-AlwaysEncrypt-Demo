# Laravel SQL Server Always Encrypted Demo

## Setup Requirements

1. SQL Server with Always Encrypted support
2. Certificate for column encryption (local store or Azure Key Vault)
3. PHP with SQL Server drivers and ODBC support

## Local Certificate Setup

Place your certificate files in the `/certs` directory:
- `PHPcert.pfx` - The certificate for SQL Server Always Encrypted

## Azure Key Vault Setup

1. Create an Azure Key Vault in your Azure subscription
2. Register an application in Azure Active Directory
3. Grant the application permissions to access keys in the Key Vault
4. Create a client secret for the application
5. Create a Key Encryption Key (KEK) and Column Encryption Key (CEK) in SQL Server

## Environment Configuration

Copy `.env.example` to `.env` and configure:
- Database connection details
- Set `CERT_PASSWORD` to your certificate password for local certificate store
- Set Azure credentials for Azure Key Vault:
  - `AZURE_CLIENT_ID`
  - `AZURE_CLIENT_SECRET`
  - `AZURE_TENANT_ID`
  - `AZURE_KEY_VAULT_URL`

## Using the Encryption in Models

```php
// Using local certificate store
$product = Product::withLocalEncryption()->find(1);

// Using Azure Key Vault
$product = Product::withAzureEncryption()->find(1);

// Create with local encryption
$product = new Product();
$product->withLocalCertStore();
$product->fill([...]);
$product->save();

// Create with Azure Key Vault encryption
$product = new Product();
$product->withAzureKeyVault();
$product->fill([...]);
$product->save();
```
