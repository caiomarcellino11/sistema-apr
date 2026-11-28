(function () {
    'use strict';

    // Endereco do Back-End.
    const BASE_URL = 'http://127.0.0.1:8000';
    const API_URL = `${BASE_URL}/api`;

    /**
     * Recupera o valor de um cookie pelo nome.
     */
    function getCookie(nome) {
        const cookies = document.cookie.split(';');

        for (const cookie of cookies) {
            const valor = cookie.trim();
            const prefixo = `${nome}=`;

            if (valor.startsWith(prefixo)) {
                return decodeURIComponent(
                    valor.substring(prefixo.length)
                );
            }
        }

        return null;
    }

    /**
     * Inicializa a protecao CSRF do Laravel Sanctum.
     */
    async function initializeCsrf() {
        const response = await fetch(
            `${BASE_URL}/sanctum/csrf-cookie`,
            {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            }
        );

        if (!response.ok) {
            throw new Error(
                'Nao foi possivel inicializar a sessao.'
            );
        }

        const csrfToken = getCookie('XSRF-TOKEN');

        if (!csrfToken) {
            throw new Error(
                'Cookie CSRF nao encontrado.'
            );
        }

        return csrfToken;
    }

    /**
     * Centraliza as requisicoes da API.
     */
    async function request(endpoint, options = {}) {
        const {
            redirectOnUnauthorized = true,
            ...fetchOptions
        } = options;

        const method = (
            fetchOptions.method || 'GET'
        ).toUpperCase();

        const headers = new Headers(
            fetchOptions.headers || {}
        );

        headers.set('Accept', 'application/json');

        // Impede o envio de credenciais para URLs externas.
        const url = new URL(`${API_URL}${endpoint}`);

        if (
            url.origin !== BASE_URL ||
            !url.pathname.startsWith('/api/')
        ) {
            throw new Error('Endpoint invalido.');
        }

        // Configura JSON quando houver corpo na requisicao.
        if (
            fetchOptions.body &&
            !(fetchOptions.body instanceof FormData)
        ) {
            headers.set(
                'Content-Type',
                'application/json'
            );
        }

        // Metodos que exigem protecao CSRF.
        const metodosSeguros = [
            'GET',
            'HEAD',
            'OPTIONS'
        ];

        if (!metodosSeguros.includes(method)) {
            let csrfToken = getCookie('XSRF-TOKEN');

            const autenticacaoInicial = [
                '/login',
                '/register'
            ].includes(endpoint);

            if (!csrfToken || autenticacaoInicial) {
                csrfToken = await initializeCsrf();
            }

            headers.set(
                'X-XSRF-TOKEN',
                csrfToken
            );
        }

        // Envia os cookies de autenticacao.
        const response = await fetch(url, {
            ...fetchOptions,
            method,
            headers,
            credentials: 'include',
            redirect: 'error'
        });

        // Sessao expirada ou usuario nao autenticado.
        if (
            response.status === 401 ||
            response.status === 419
        ) {
            if (redirectOnUnauthorized) {
                window.location.replace(
                    'index.html?session=expired'
                );
            }
        }

        return response;
    }

    // Disponibiliza o servico para as paginas HTML.
    window.AprApi = Object.freeze({
        request,
        initializeCsrf
    });

})();