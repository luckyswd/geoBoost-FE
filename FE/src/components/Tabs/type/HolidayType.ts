export type HolidaySetTagResponse = {
    value: boolean;
}

export type HolidaysResponse = {
    page?: number;
    totalCount: number;
    items: Holiday[];
}

export type Holiday = {
    id: number;
    name: string;
    tags: string[];
};