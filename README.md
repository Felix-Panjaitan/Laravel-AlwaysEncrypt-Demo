# Laravel SQL Server Always Encrypted Demo

## Setup Requirements

1. SQL Server with Always Encrypted support
2. Certificate for column encryption
3. PHP with SQL Server drivers and ODBC support

## Certificate Setup

Place your certificate files in the `/certs` directory:
- `PHPcert.pfx` - The certificate for SQL Server Always Encrypted

## Environment Configuration

Copy `.env.example` to `.env` and configure:
- Database connection details
- Set `CERT_PASSWORD` to your certificate password
- Configure other application settings
