import {useState, useCallback, useEffect} from 'react';
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
    SkeletonBodyText, Text,
} from '@shopify/polaris';
import {ApiV1Response, ERROR_MESSAGE} from "../../type/global";
import {apiFetch} from "../../api";
import {Holiday, HolidaySetTagResponse, HolidaysResponse} from "./type/HolidayType";
import {useDebouncedCallback} from "use-debounce";

export function Holidays() {
    const [searchQuery, setSearchQuery] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [holidays, setHolidays] = useState<Holiday[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [currentHoliday, setCurrentHoliday] = useState<Holiday>();
    const [newTag, setNewTag] = useState('');
    const [totalCount, setTotalCount] = useState(0);
    const itemsPerPage = 12;

    useEffect(() => {
        fetchHolidays(currentPage);
    }, []);

    const fetchHolidays = async (page: number) => {
        setIsLoading(true);

        const response = await apiFetch<ApiV1Response<HolidaysResponse>>(`/holiday/?page=${page}&s=${searchQuery}`, {
            method: "GET"
        });

        setHolidays(response?.data?.items || []);
        setTotalCount(response?.data?.totalCount || 0);
        setCurrentPage(response?.data?.page || 1);

        setIsLoading(false);
    };

    const debouncedSearch = useDebouncedCallback(async () => {
        await fetchHolidays(currentPage);
    }, 300);

    const handleSearch = useCallback((value: string) => {
        setSearchQuery(value);
        setCurrentPage(1);
        debouncedSearch();
    }, [debouncedSearch]);

    const removeTag = useCallback(async (holiday: Holiday, tag: string) => {
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

        setHolidays((prevTags) =>
            prevTags.map((prevHoliday) =>
                prevHoliday.id === holiday.id
                    ? {...prevHoliday, tags: prevHoliday.tags.filter((t) => t !== tag)}
                    : prevHoliday
            )
        );

        shopify.toast.show(`Tag "${tag}" successfully removed`);
    }, []);

    const openModal = (holiday: Holiday) => {
        setCurrentHoliday(holiday);
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setNewTag('');
    };

    const addNewTag = async () => {
        if (!newTag.trim() || !currentHoliday) {
            return;
        }

        if (currentHoliday.tags.includes(newTag)) {
            shopify.toast.show(`Tag "${newTag}" already exists`);
            closeModal();
            return;
        }

        await apiFetch<ApiV1Response<HolidaySetTagResponse>>(
            `/holiday/${currentHoliday.id}/tag`,
            {
                method: "PATCH",
                data: {
                    action: "add",
                    tag: newTag,
                },
            }
        );

        setHolidays((prevTags) =>
            prevTags.map((holiday) =>
                holiday.id === currentHoliday.id
                    ? { ...holiday, tags: [...holiday.tags, newTag] }
                    : holiday
            )
        );

        shopify.toast.show(`Tag "${newTag}" successfully added`);
        closeModal();
    };

    const handlePagination = async (action: string) => {
        const newPage = action === 'onPrevious'
            ? Math.max(Number(currentPage) - 1, 1)
            : Number(currentPage) + 1;

        if (newPage !== currentPage) {
            await fetchHolidays(newPage);
            setCurrentPage(newPage);
        }
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
                    placeholder="Search for a holiday..."
                    variant="borderless"
                />
            </Card>

            <Card>
                {isLoading ? (
                    <SkeletonBodyText lines={12} />
                ) : (
                    <>
                        <DataTable
                            columnContentTypes={['text', 'text', 'text']}
                            headings={['Name', 'Tags', 'Action']}
                            rows={holidays?.map((holiday) => {
                                const allTags = [
                                    ...(holiday.tags || []),
                                ];

                                return [
                                    holiday.name,
                                    <InlineStack gap="300">
                                        {allTags.length > 0 ? (
                                            allTags.map((tag) => (
                                                <Tag key={tag} onRemove={() => removeTag(holiday, tag)}>
                                                    {tag}
                                                </Tag>
                                            ))
                                        ) : (
                                            <span>No tags</span>
                                        )}
                                    </InlineStack>,
                                    <Button variant="primary" onClick={() => openModal(holiday)}>Add New Tag</Button>,
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

            <Modal
                open={isModalOpen}
                onClose={closeModal}
                title={`Add a new tag for ${currentHoliday?.name}`}
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
