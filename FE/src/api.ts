const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;

const defaultHeaders = {
    'Content-Type': 'application/json',
    'ngrok-skip-browser-warning': 'true',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET',
    'Access-Control-Allow-Headers': 'Origin, x-app-referer, Content-Type, Accept',
    'Accept': '*/*',
    'x-app-referer': window.location.href,
};

interface ApiFetchOptions {
    method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
    data?: Record<string, any>;
    headers?: Record<string, string>;
    [key: string]: any;
}

async function apiFetch<T>(endpoint: string, options: ApiFetchOptions = {}): Promise<T> {
    const { method = 'GET', data, headers, ...rest } = options;
    const domain = shopify.config.shop;

    let finalEndpoint = endpoint;
    let finalBody = data ? { ...data } : undefined;

    if (method.toUpperCase() === 'GET') {
        const separator = endpoint.includes('?') ? '&' : '?';
        finalEndpoint = `${endpoint}${separator}domain=${encodeURIComponent(domain || '')}`;
    } else {
        finalBody = {
            ...data,
            domain,
        };
    }

    const response = await fetch(`${BACKEND_URL}${finalEndpoint}`, {
        method,
        headers: {
            ...defaultHeaders,
            ...headers,
            Authorization: `Bearer ${await shopify.idToken()}`
        },
        body: finalBody ? JSON.stringify(finalBody) : undefined,
        ...rest,
    });

    if (!response.ok) {
        const errorData = await response.json();

        throw new Error(errorData.message || 'Something went wrong');
    }

    return await response.json() as T;
}

export { apiFetch };
