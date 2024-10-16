export type ApiV1Response<T, M = any> = {
    data?: T;
    meta?: M;
    errors?: string
};