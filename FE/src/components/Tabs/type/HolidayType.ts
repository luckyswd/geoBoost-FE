export type HolidaySetTagResponse = {
    value: boolean;
}

export type HolidaysResponse = {
    page?: number;
    totalCount: number;
    items: Holiday[];
    countries: string[];
    years: string[];
}

export type HolidayNamesResponse = {
    name: string;
    countries: string;
}

export type HolidayNamesFilter = {
    label: string;
    value: string;
}

export type Holiday = {
    id: number;
    name: string;
    country: string;
    holidayDate: string;
    year: number;
};