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
    SkeletonBodyText, Text, Link, Badge,
} from '@shopify/polaris';
import {ApiV1Response, ERROR_MESSAGE} from "../../type/global";
import {apiFetch} from "../../api";
import {HolidaySetTagResponse} from "./type/HolidayType";
import {useDebouncedCallback} from "use-debounce";
import {HolidayTag, Product, ProductsResponse} from "./type/ProductType";
import {Tone} from "@shopify/polaris/build/ts/src/components/Badge";

export function ProductTag() {
    const [searchQuery, setSearchQuery] = useState('');
    const [products, setProducts] = useState<Product[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [currentProduct, setCurrentProduct] = useState<Product>();
    const [newTag, setNewTag] = useState('');
    const [hasNextPage, setHasNextPage] = useState(false);
    const [hasPreviousPage, setHasPreviousPage] = useState(false);
    const [startCursor, setStartCursor] = useState('');
    const [endCursor, setEndCursor] = useState('');

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
            queryString.append('after', startCursor);
        }

        if (pagination === 'prev' && endCursor.length > 0) {
            queryString.append('before', endCursor);
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
    }, 200);

    const handleSearch = useCallback((value: string) => {
        setSearchQuery(value);
        debouncedSearch();
    }, [debouncedSearch]);

    const removeTag = useCallback(async (holiday: Product, tag: HolidayTag) => {
        const response = await apiFetch<ApiV1Response<HolidaySetTagResponse>>(`/holiday/${holiday.id}/tag`, {
            method: "PATCH",
            data: {
                action: "remove",
                tag: tag,
            }
        });

        if (response.errors) {
            shopify.toast.show(ERROR_MESSAGE, {
                isError: true
            });
            return;
        }

        // setProducts((prevTags) =>
        //     prevTags.map((prevHoliday) =>
        //         prevHoliday.id === holiday.id
        //             ? {...prevHoliday, tags: prevHoliday.tags.filter((t) => t !== tag)}
        //             : prevHoliday
        //     )
        // );

        shopify.toast.show(`Tag "${tag}" successfully removed`);
    }, []);

    const openModal = (product: Product) => {
        setCurrentProduct(product);
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setNewTag('');
    };

    const addNewTag = async () => {
        if (newTag.trim() !== '' && currentProduct) {
            const response = await apiFetch<ApiV1Response<HolidaySetTagResponse>>(`/holiday/${currentProduct.id}/tag`, {
                method: "PATCH",
                data: {
                    action: "add",
                    tag: newTag,
                }
            });

            if (response.errors) {
                shopify.toast.show(ERROR_MESSAGE, {
                    isError: true
                });
                return;
            }

            // setProducts((prevTags) =>
            //     prevTags.map((holiday) =>
            //         holiday.id === currentHoliday.id
            //             ? {...holiday, tags: [...holiday.tags, newTag]}
            //             : holiday
            //     )
            // );

            shopify.toast.show(`Tag "${newTag}" successfully added`);
            closeModal();
        }
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
                            headings={['Product', 'Status', 'Collection', 'Holiday Tags', 'Action']}
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
                                    {product.holidayTags && product.holidayTags.map((tag) => (
                                        <Tag key={tag.key} onRemove={() => removeTag(product, tag)}>
                                            {tag.value}
                                        </Tag>
                                    ))}
                                </InlineStack>,
                                <Button variant="primary" onClick={() => openModal(product)}>Add New Tag</Button>,
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
                title={`Add a new tag for ${currentProduct?.title}`}
                primaryAction={{
                    content: 'Add',
                    onAction: addNewTag,
                }}
            >
                <Modal.Section>
                    <TextField
                        label="New Tag"
                        value={newTag}
                        onChange={(value) => setNewTag(value)}
                        autoComplete="off"
                        placeholder="Enter new tag"
                    />
                </Modal.Section>
            </Modal>
        </BlockStack>
    );
}
