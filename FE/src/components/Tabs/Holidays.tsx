import { useState, useCallback, useEffect } from 'react';
import {
    Card,
    DataTable,
    TextField,
    BlockStack,
    Pagination,
    SkeletonBodyText,
    Text,
    Select, InlineGrid,
} from '@shopify/polaris';
import { ApiV1Response } from "../../type/global";
import { apiFetch } from "../../api";
import { Holiday, HolidaysResponse } from "./type/HolidayType";
import { useDebouncedCallback } from "use-debounce";

export function Holidays() {
    const [searchQuery, setSearchQuery] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [holidays, setHolidays] = useState<Holiday[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [totalCount, setTotalCount] = useState(0);
    const [country, setCountry] = useState<string>('');
    const [countries, setCountries] = useState<string[]>([]);
    const [year, setYear] = useState<string>('');
    const [years, setYears] = useState<string[]>([]);
    const itemsPerPage = 12;

    const fetchHolidays = async (page: number) => {
        setIsLoading(true);

        let queryParams = `?page=${page}&s=${searchQuery}`;

        if (country) {
            queryParams += `&country=${country}`;
        }

        if (year) {
            queryParams += `&year=${year}`;
        }

        const response = await apiFetch<ApiV1Response<HolidaysResponse>>(`/holiday/${queryParams}`, {
            method: "GET"
        });

        setHolidays(response?.data?.items || []);
        setCountries(response?.data?.countries || []);
        setYears(response?.data?.years || []);
        setTotalCount(response?.data?.totalCount || 0);
        setCurrentPage(response?.data?.page || 1);

        setIsLoading(false);
    };

    useEffect(() => {
        fetchHolidays(currentPage);
    }, [country, year]);

    const debouncedSearch = useDebouncedCallback(async () => {
        await fetchHolidays(currentPage);
    }, 300);

    const handleSearch = useCallback((value: string) => {
        setSearchQuery(value);
        setCurrentPage(1);
        debouncedSearch();
    }, [debouncedSearch]);

    const handlePagination = async (action: string) => {
        const newPage = action === 'onPrevious'
            ? Math.max(Number(currentPage) - 1, 1)
            : Number(currentPage) + 1;

        if (newPage !== currentPage) {
            await fetchHolidays(newPage);
            setCurrentPage(newPage);
        }
    };

    const countryOptions = [
        { label: 'All', value: '' },
        ...countries.map(country => ({
            label: country,
            value: country
        }))
    ];

    const yearOptions = years.map(year => ({
        label: year,
        value: year
    }));

    return (
        <BlockStack gap="200">
            <Card>
                <Text variant="headingMd" as="h3" alignment="center">
                    Here you can explore all the holidays celebrated in our system, as well as the tags associated with them.<br />
                    The user-friendly interface allows you to easily manage tags by adding new ones or deleting existing ones
                </Text>
            </Card>

            <Card>
                <InlineGrid gap="400" columns={2}>
                    <Select
                        label="Select Country"
                        options={countryOptions}
                        value={country}
                        onChange={(value) => setCountry(value)}
                    />
                    <Select
                        label="Select Year"
                        options={yearOptions}
                        value={year}
                        onChange={(value) => setYear(value)}
                    />
                </InlineGrid>
            </Card>

            <Card>
                <TextField
                    label=""
                    value={searchQuery}
                    onChange={handleSearch}
                    autoComplete="off"
                    placeholder="Search by holiday name..."
                    variant="borderless"
                />
            </Card>

            <Card>
                {isLoading ? (
                    <SkeletonBodyText lines={12} />
                ) : (
                    <>
                        <DataTable
                            columnContentTypes={['text', 'text', 'numeric', 'text']}
                            headings={['Name', 'Country', 'Year', 'Holiday date']}
                            rows={holidays?.map((holiday) => {
                                return [
                                    holiday.name,
                                    holiday.country,
                                    holiday.year,
                                    holiday.holidayDate,
                                ];
                            }) || []}
                        />

                        <Pagination
                            hasPrevious={currentPage > 1}
                            onPrevious={() => handlePagination('onPrevious')}
                            hasNext={currentPage * itemsPerPage < totalCount}
                            onNext={() => handlePagination('onNext')}
                            label={`Showing ${Math.min((currentPage - 1) * itemsPerPage + 1, totalCount)}-${Math.min(currentPage * itemsPerPage, totalCount)} of ${totalCount}`}
                            type="table"
                        />
                    </>
                )}
            </Card>
        </BlockStack>
    );
}
