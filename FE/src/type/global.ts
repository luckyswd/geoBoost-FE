export const ERROR_MESSAGE = 'An error has occurred, please contact support'

export type ApiV1Response<T, M = any> = {
    data?: T;
    meta?: M;
    errors?: string
};