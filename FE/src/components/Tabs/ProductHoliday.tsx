import React, {useState, useCallback, useEffect} from 'react';
import {
    Card,
    DataTable,
    TextField,
    Button,
    Modal,
    Tag,
    BlockStack,
    Pagination,
    InlineStack,
    SkeletonBodyText, Text, Link, Badge, Autocomplete, LegacyStack,
} from '@shopify/polaris';
import {ApiV1Response, ERROR_MESSAGE} from "../../type/global";
import {apiFetch} from "../../api";
import {HolidayNamesFilter, HolidayNamesResponse} from "./type/HolidayType";
import {useDebouncedCallback} from "use-debounce";
import {Product, ProductsResponse} from "./type/ProductType";
import {Tone} from "@shopify/polaris/build/ts/src/components/Badge";

export function ProductHoliday() {
    const [searchQuery, setSearchQuery] = useState('');
    const [products, setProducts] = useState<Product[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [currentProduct, setCurrentProduct] = useState<Product>();
    const [hasNextPage, setHasNextPage] = useState(false);
    const [hasPreviousPage, setHasPreviousPage] = useState(false);
    const [startCursor, setStartCursor] = useState('');
    const [endCursor, setEndCursor] = useState('');
    const [selectedHolidaysNameOptions, setSelectedHolidaysNameOptions] = useState<string[]>([]);
    const [stringValueHolidayName, setStringValueHolidayName] = useState('');
    const [filterHolidayNameOptions, setFilterHolidayNameOptions] = useState<HolidayNamesFilter[]>([]);
    const [holidaysNameOptions, setHolidaysNameOptions] = useState(filterHolidayNameOptions);

    useEffect(() => {
        fetchProducts();
    }, []);

    const fetchProducts = async (pagination: string | undefined = undefined) => {
        setIsLoading(true);

        let queryString = new URLSearchParams();

        if (searchQuery.length > 0) {
            queryString.append('s', searchQuery);
        }

        if (pagination === 'next' && startCursor.length > 0) {
            queryString.append('after', endCursor);
        }

        if (pagination === 'prev' && endCursor.length > 0) {
            queryString.append('before', startCursor);
        }

        const response = await apiFetch<ApiV1Response<ProductsResponse>>(`/product/?${queryString.toString()}`, {
            method: "GET"
        });

        setProducts(response?.data?.products || [])
        setHasNextPage(response?.data?.pageInfo.hasNextPage || false);
        setHasPreviousPage(response?.data?.pageInfo.hasPreviousPage || false);
        setStartCursor(response?.data?.pageInfo.startCursor || '');
        setEndCursor(response?.data?.pageInfo.endCursor || '');

        setIsLoading(false);
    };

    const debouncedSearch = useDebouncedCallback(async () => {
        await fetchProducts();
    }, 300);

    const handleSearch = useCallback((value: string) => {
        setSearchQuery(value);
        debouncedSearch();
    }, [debouncedSearch]);

    const openModal = (product: Product) => {
        setCurrentProduct(product);
        fetchHolidayNames();
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        // setNewTag('');
    };

    const addHoliday = async () => {
        try {
            await apiFetch(`/product/set`, {
                method: 'POST',
                data: {
                    productId: currentProduct?.id,
                    holidayNames: selectedHolidaysNameOptions.join(','),
                },
            });

            shopify.toast.show(`Holidays successfully added`);
        } catch (error) {
            shopify.toast.show(ERROR_MESSAGE, {
                isError: true
            });
        }

        closeModal();
    };

    const capitalizeFirstLetter = (text: string): string => {
        return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
    };

    const getToneForStatus = (status: string): Tone | undefined => {
        const tones: Record<string, Tone> = {
            ACTIVE: 'success',
            DRAFT: 'info',
        };

        return tones[status] || undefined;
    };

    const trimShopifyDomain = (url: string | undefined): string => {
        return url?.replace('.myshopify.com', '') || '';
    };

    const fetchHolidayNames = async () => {
        setIsLoading(true);

        const response = await apiFetch<ApiV1Response<HolidayNamesResponse[]>>(`/holiday/names`, {
            method: "GET"
        });

        const data = response?.data || [];

        const optionsHolidayNames = data.map((holidayName) => {
            const countriesArray = holidayName.countries.split(",");
            const displayedCountries = countriesArray.slice(0, 3).join(", ");
            const remainingCount = countriesArray.length - 3;

            const countriesLabel = remainingCount > 0
                ? `${displayedCountries} and ${remainingCount} more`
                : displayedCountries;

            return {
                label: `${holidayName.name} (${countriesLabel})`,
                value: holidayName.name,
            };
        });

        setFilterHolidayNameOptions(optionsHolidayNames);
        setHolidaysNameOptions(optionsHolidayNames)
        setIsLoading(false);
    };

    const updateText = useCallback(
        (value: string) => {
            setStringValueHolidayName(value);

            if (value === '') {
                setHolidaysNameOptions(filterHolidayNameOptions);
                return;
            }

            const filterRegex = new RegExp(value, 'i');
            const resultOptions = filterHolidayNameOptions.filter((option) =>
                option.label.match(filterRegex),
            );

            setHolidaysNameOptions(resultOptions);
        },
        [filterHolidayNameOptions],
    );

    const removeTag = useCallback(
        (tag: string) => () => {
            const options = [...selectedHolidaysNameOptions];
            options.splice(options.indexOf(tag), 1);
            setSelectedHolidaysNameOptions(options);
        },
        [selectedHolidaysNameOptions],
    );

    const verticalContentMarkup =
        selectedHolidaysNameOptions.length > 0 ? (
            <LegacyStack spacing="extraTight" alignment="center">
                {selectedHolidaysNameOptions.map((option) => {
                    let tagLabel = '';
                    tagLabel = option.replace('_', ' ');
                    tagLabel = titleCase(tagLabel);
                    return (
                        <Tag key={`option${option}`} onRemove={removeTag(option)}>
                            {tagLabel}
                        </Tag>
                    );
                })}
            </LegacyStack>
        ) : null;

    return (
        <BlockStack gap="200">
            <Card>
                <Text variant="headingMd" as="h3" alignment="center">
                    Here you can explore all the holidays celebrated in our system, as well as the tags associated with them.<br/>
                    The user-friendly interface allows you to easily manage tags by adding new ones or deleting existing ones
                </Text>
            </Card>

            <Card>
                <TextField
                    label=""
                    value={searchQuery}
                    onChange={handleSearch}
                    autoComplete="off"
                    placeholder="Search for a product..."
                    variant="borderless"
                />
            </Card>

            <Card>
                {isLoading ? (
                    <SkeletonBodyText lines={12} />
                ) : (
                    <>
                        <DataTable
                            columnContentTypes={['text', 'text', 'text', 'text', 'text', 'text']}
                            headings={['Product', 'Status', 'Collection', 'Holidays', 'Action']}
                            rows={products.map((product) => [
                                <Link
                                    url={`https://admin.shopify.com/store/${trimShopifyDomain(shopify.config.shop)}/products/${product.id}`}
                                    target="_blank"
                                    monochrome
                                >
                                    {product.title}
                                </Link>,
                                <Badge
                                    {...(getToneForStatus(product.status) ? { tone: getToneForStatus(product.status) } : {})}
                                >
                                    {capitalizeFirstLetter(product.status)}
                                </Badge>,
                                <InlineStack gap="300">
                                    {product.collection && product.collection.map((collection) => (
                                        <Link url={`https://admin.shopify.com/store/${trimShopifyDomain(shopify.config.shop)}/collections/${collection.id}`} target="_blank">
                                            <Tag key={collection.id}>
                                                {collection.title}
                                            </Tag>
                                        </Link>
                                    ))}
                                </InlineStack>,
                                <InlineStack gap="300">
                                    {product.holidayNames && product.holidayNames.map((name) => (
                                        <Tag key={name}>
                                            {name}
                                        </Tag>
                                    ))}
                                </InlineStack>,
                                <Button variant="primary" onClick={() => openModal(product)}>Add holidays</Button>,
                            ])}
                        />

                        <Pagination
                            hasPrevious={hasPreviousPage}
                            onPrevious={() => fetchProducts('prev')}
                            hasNext={hasNextPage}
                            onNext={() => fetchProducts('next')}
                            type="table"
                        />
                    </>
                )}
            </Card>

            <Modal
                open={isModalOpen}
                onClose={closeModal}
                title={`Add holidays for "${currentProduct?.title}"`}
                primaryAction={{
                    content: 'Save',
                    onAction: addHoliday,
                }}
            >
                <Modal.Section>
                    <>
                        {isLoading ? (
                            <SkeletonBodyText lines={6} />
                        ) : (
                            <>
                                <Autocomplete
                                    allowMultiple
                                    options={holidaysNameOptions}
                                    selected={selectedHolidaysNameOptions}
                                    textField={
                                        <Autocomplete.TextField
                                            onChange={updateText}
                                            label="Holidays"
                                            value={stringValueHolidayName}
                                            placeholder="New Year’s Day, Christmas ..."
                                            verticalContent={verticalContentMarkup}
                                            autoComplete="off"
                                        />
                                    }
                                    onSelect={setSelectedHolidaysNameOptions}
                                    listTitle="Suggested Tags"
                                />
                            </>
                        )}
                    </>
                </Modal.Section>
            </Modal>
        </BlockStack>
    );

    function titleCase(string: string) {
        return string
            .toLowerCase()
            .split(' ')
            .map((word) => word.replace(word[0], word[0].toUpperCase()))
            .join('');
    }
}
