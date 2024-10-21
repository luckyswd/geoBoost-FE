export type ProductsResponse = {
    products: Product[];
    pageInfo: {
        hasNextPage: boolean;
        hasPreviousPage: boolean;
        endCursor: string;
        startCursor: string;
    }
}

export type Product = {
    id: number;
    handle: string;
    title: string;
    status: string;
    holidayTags: HolidayTag[];
    collection: Collection[];
};

export type Collection = {
    id: string;
    title: string;
}

export type HolidayTag = {
    key: string;
    value: string
}