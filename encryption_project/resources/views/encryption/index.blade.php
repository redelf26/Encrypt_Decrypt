<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AES Encryption Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 40px;
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .nav-tabs .nav-link.active {
            background-color: #f8f9fa;
            border-bottom-color: #f8f9fa;
            font-weight: bold;
        }
        .result-box {
            background-color: #f0f0f0;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
            word-break: break-all;
        }
        .copy-btn {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1>AES Encryption & Decryption Tool</h1>
                <p class="lead">Securely encrypt and decrypt text using AES-256 encryption</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="cryptoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ session('mode') != 'decrypt' ? 'active' : '' }}" 
                                id="encrypt-tab" data-bs-toggle="tab" data-bs-target="#encrypt-pane" 
                                type="button" role="tab" aria-controls="encrypt-pane" aria-selected="true">
                            Encrypt
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ session('mode') == 'decrypt' ? 'active' : '' }}" 
                                id="decrypt-tab" data-bs-toggle="tab" data-bs-target="#decrypt-pane" 
                                type="button" role="tab" aria-controls="decrypt-pane" aria-selected="false">
                            Decrypt
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="cryptoTabsContent">
                    <!-- Encrypt Tab Pane -->
                    <div class="tab-pane fade {{ session('mode') != 'decrypt' ? 'show active' : '' }}" id="encrypt-pane" role="tabpanel" aria-labelledby="encrypt-tab">
                        <form action="{{ route('encryption.encrypt') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="text" class="form-label">Text to Encrypt</label>
                                <textarea class="form-control" id="text" name="text" rows="5" required>{{ old('text') }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="secret_key" class="form-label">Secret Key</label>
                                        <input type="text" class="form-control" id="secret_key" name="secret_key" value="{{ old('secret_key') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="iv" class="form-label">Initialization Vector (IV) <small class="text-muted">(Optional)</small></label>
                                        <input type="text" class="form-control" id="iv" name="iv" value="{{ old('iv') }}">
                                        <div class="form-text">Leave blank to auto-generate</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="key_size" class="form-label">Key Size</label>
                                        <select class="form-select" id="key_size" name="key_size">
                                            <option value="128" {{ old('key_size') == '128' ? 'selected' : '' }}>128 bits</option>
                                            <option value="192" {{ old('key_size') == '192' ? 'selected' : '' }}>192 bits</option>
                                            <option value="256" {{ old('key_size', '256') == '256' ? 'selected' : '' }}>256 bits</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="cipher_mode" class="form-label">Cipher Mode</label>
                                        <select class="form-select" id="cipher_mode" name="cipher_mode">
                                            <option value="CBC" {{ old('cipher_mode', 'CBC') == 'CBC' ? 'selected' : '' }}>CBC</option>
                                            <option value="CFB" {{ old('cipher_mode') == 'CFB' ? 'selected' : '' }}>CFB</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="padding" class="form-label">Padding</label>
                                        <select class="form-select" id="padding" name="padding">
                                            <option value="PKCS7" {{ old('padding', 'PKCS7') == 'PKCS7' ? 'selected' : '' }}>PKCS7</option>
                                            <option value="NoPadding" {{ old('padding') == 'NoPadding' ? 'selected' : '' }}>No Padding</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="output_format" class="form-label">Output Format</label>
                                        <select class="form-select" id="output_format" name="output_format">
                                            <option value="base64" {{ old('output_format', 'base64') == 'base64' ? 'selected' : '' }}>Base64</option>
                                            <option value="hex" {{ old('output_format') == 'hex' ? 'selected' : '' }}>Hexadecimal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Encrypt</button>
                            </div>
                        </form>
                        
                        @if(session('result'))
                            <div class="mt-4">
                                <h5>Encrypted Result:</h5>
                                <div class="result-box">
                                    <div class="d-flex justify-content-between">
                                        <strong>Encrypted Text ({{ session('output_format') == 'hex' ? 'Hex' : 'Base64' }}):</strong>
                                        <button class="btn btn-sm btn-outline-secondary copy-btn" 
                                                onclick="copyToClipboard('encrypted-result')">
                                            Copy
                                        </button>
                                    </div>
                                    <div id="encrypted-result" class="mt-2">{{ session('result') }}</div>
                                </div>
                                
                                <div class="result-box mt-3">
                                    <div class="d-flex justify-content-between">
                                        <strong>Initialization Vector (IV):</strong>
                                        <button class="btn btn-sm btn-outline-secondary copy-btn" 
                                                onclick="copyToClipboard('iv-result')">
                                            Copy
                                        </button>
                                    </div>
                                    <div id="iv-result" class="mt-2">{{ session('iv') }}</div>
                                    <div class="form-text text-danger">Keep this IV for decryption!</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Decrypt Tab Pane -->
                    <div class="tab-pane fade {{ session('mode') == 'decrypt' ? 'show active' : '' }}" id="decrypt-pane" role="tabpanel" aria-labelledby="decrypt-tab">
                        <form action="{{ route('encryption.decrypt') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="encrypted_text" class="form-label">Encrypted Text</label>
                                <textarea class="form-control" id="encrypted_text" name="encrypted_text" rows="5" required>{{ old('encrypted_text') }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="decrypt_secret_key" class="form-label">Secret Key</label>
                                        <input type="text" class="form-control" id="decrypt_secret_key" name="secret_key" value="{{ old('secret_key') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="decrypt_iv" class="form-label">Initialization Vector (IV)</label>
                                        <input type="text" class="form-control" id="decrypt_iv" name="iv" value="{{ old('iv') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="decrypt_key_size" class="form-label">Key Size</label>
                                        <select class="form-select" id="decrypt_key_size" name="key_size">
                                            <option value="128" {{ old('key_size') == '128' ? 'selected' : '' }}>128 bits</option>
                                            <option value="192" {{ old('key_size') == '192' ? 'selected' : '' }}>192 bits</option>
                                            <option value="256" {{ old('key_size', '256') == '256' ? 'selected' : '' }}>256 bits</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="decrypt_cipher_mode" class="form-label">Cipher Mode</label>
                                        <select class="form-select" id="decrypt_cipher_mode" name="cipher_mode">
                                            <option value="CBC" {{ old('cipher_mode', 'CBC') == 'CBC' ? 'selected' : '' }}>CBC</option>
                                            <option value="CFB" {{ old('cipher_mode') == 'CFB' ? 'selected' : '' }}>CFB</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="decrypt_padding" class="form-label">Padding</label>
                                        <select class="form-select" id="decrypt_padding" name="padding">
                                            <option value="PKCS7" {{ old('padding', 'PKCS7') == 'PKCS7' ? 'selected' : '' }}>PKCS7</option>
                                            <option value="NoPadding" {{ old('padding') == 'NoPadding' ? 'selected' : '' }}>No Padding</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="input_format" class="form-label">Input Format</label>
                                        <select class="form-select" id="input_format" name="input_format">
                                            <option value="base64" {{ old('input_format', 'base64') == 'base64' ? 'selected' : '' }}>Base64</option>
                                            <option value="hex" {{ old('input_format') == 'hex' ? 'selected' : '' }}>Hexadecimal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-info btn-lg">Decrypt</button>
                            </div>
                        </form>
                        
                        @if(session('decrypted'))
                            <div class="mt-4">
                                <h5>Decryption Result:</h5>
                                <div class="result-box">
                                    <div class="d-flex justify-content-between">
                                        <strong>Decrypted Text:</strong>
                                        <button class="btn btn-sm btn-outline-secondary copy-btn" 
                                                onclick="copyToClipboard('decrypted-result')">
                                            Copy
                                        </button>
                                    </div>
                                    <div id="decrypted-result" class="mt-2">{{ session('decrypted') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Information About AES Encryption
                    </div>
                    <div class="card-body">
                        <h5>Features of this tool:</h5>
                        <ul>
                            <li>AES encryption with customizable key sizes (128, 192, 256 bits)</li>
                            <li>Support for CBC (Cipher Block Chaining) and CFB (Cipher Feedback) cipher modes</li>
                            <li>PKCS7 padding and No Padding options</li>
                            <li>Custom or auto-generated Initialization Vector (IV)</li>
                            <li>Output in Base64 or Hexadecimal format</li>
                        </ul>
                        
                        <h5>Security Best Practices:</h5>
                        <ul>
                            <li>Always use strong, unique secret keys</li>
                            <li>Keep your IV secure but remember it's necessary for decryption</li>
                            <li>AES-256 is the most secure key size option</li>
                            <li>CBC mode is generally recommended for most use cases</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <footer class="mt-5 text-center text-muted">
            <p>AES Encryption & Decryption Tool • Built with Laravel</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.innerText;
            
            navigator.clipboard.writeText(text).then(() => {
                const button = event.target;
                const originalText = button.innerText;
                button.innerText = 'Copied!';
                setTimeout(() => {
                    button.innerText = originalText;
                }, 1000);
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            if (hash === '#decrypt') {
                const decryptTab = document.getElementById('decrypt-tab');
                if (decryptTab) {
                    decryptTab.click();
                }
            }
        });
    </script>
</body>
</html>