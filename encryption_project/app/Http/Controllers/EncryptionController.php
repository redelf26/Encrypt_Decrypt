<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EncryptionController extends Controller
{
    /**
     * Show the encryption/decryption form
     */
    public function index()
    {
        return view('encryption.index');
    }
    
    /**
     * Handle the encryption request
     */
    public function encrypt(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'text' => 'required|string',
            'secret_key' => 'required|string',
            'cipher_mode' => 'required|in:CBC,CFB',
            'padding' => 'required|in:PKCS7,NoPadding',
            'iv' => 'nullable|string',
            'key_size' => 'required|in:128,192,256',
            'output_format' => 'required|in:base64,hex',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            // Get validated data
            $data = $validator->validated();
            
            // Process the key based on key size (in bits, we need bytes)
            $keySize = (int)$data['key_size'] / 8;
            $key = substr(hash('sha256', $data['secret_key'], true), 0, $keySize);
            
            // Prepare the cipher method
            $cipherMethod = 'aes-' . $data['key_size'] . '-' . strtolower($data['cipher_mode']);
            
            // Handle IV (Initialization Vector)
            $iv = $data['iv'] ? substr(hash('sha256', $data['iv'], true), 0, 16) : openssl_random_pseudo_bytes(16);
            
            // Set padding options
            $options = 0;
            if ($data['padding'] === 'NoPadding') {
                $options = OPENSSL_RAW_DATA;
            }
            
            // Encrypt the data
            $encrypted = openssl_encrypt(
                $data['text'],
                $cipherMethod,
                $key,
                $options,
                $iv
            );
            
            if ($encrypted === false) {
                throw new \Exception('Encryption failed: ' . openssl_error_string());
            }
            
            // Format the output
            if ($data['output_format'] === 'hex') {
                $result = bin2hex($encrypted);
                $ivOutput = bin2hex($iv);
            } else {
                $result = base64_encode($encrypted);
                $ivOutput = base64_encode($iv);
            }
            
            return back()->with([
                'result' => $result,
                'iv' => $ivOutput,
                'mode' => 'encrypt',
                'output_format' => $data['output_format']
            ])->withInput();
            
        } catch (\Exception $e) {
            return back()->withErrors(['encryption_error' => $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Handle the decryption request
     */
    public function decrypt(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'encrypted_text' => 'required|string',
            'secret_key' => 'required|string',
            'cipher_mode' => 'required|in:CBC,CFB',
            'padding' => 'required|in:PKCS7,NoPadding',
            'iv' => 'required|string', // IV is required for decryption
            'key_size' => 'required|in:128,192,256',
            'input_format' => 'required|in:base64,hex',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            // Get validated data
            $data = $validator->validated();
            
            // Process the key based on key size (in bits, we need bytes)
            $keySize = (int)$data['key_size'] / 8;
            $key = substr(hash('sha256', $data['secret_key'], true), 0, $keySize);
            
            // Prepare the cipher method
            $cipherMethod = 'aes-' . $data['key_size'] . '-' . strtolower($data['cipher_mode']);
            
            // Decode the input based on format
            if ($data['input_format'] === 'hex') {
                $encryptedData = hex2bin($data['encrypted_text']);
                $iv = hex2bin($data['iv']);
            } else {
                $encryptedData = base64_decode($data['encrypted_text']);
                $iv = base64_decode($data['iv']);
            }
            
            // Set padding options
            $options = 0;
            if ($data['padding'] === 'NoPadding') {
                $options = OPENSSL_RAW_DATA;
            }
            
            // Decrypt the data
            $decrypted = openssl_decrypt(
                $encryptedData,
                $cipherMethod,
                $key,
                $options,
                $iv
            );
            
            if ($decrypted === false) {
                throw new \Exception('Decryption failed: ' . openssl_error_string());
            }
            
            return back()->with([
                'decrypted' => $decrypted,
                'mode' => 'decrypt'
            ])->withInput();
            
        } catch (\Exception $e) {
            return back()->withErrors(['decryption_error' => $e->getMessage()])->withInput();
        }
    }
}
