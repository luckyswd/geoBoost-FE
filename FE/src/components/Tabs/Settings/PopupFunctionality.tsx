import { ButtonGroup, Button, Text, BlockStack, Card } from '@shopify/polaris';
import { useEffect, useState } from 'react';
import {apiFetch} from "../../../api";

interface PopupResponse {
    data: {
        value: boolean;
    };
}

export default function PopupFunctionality() {
    const [isPopupEnabled, setIsPopupEnabled] = useState(true);

    const fetchPopupStatus = async () => {
        try {
            const data = await apiFetch<PopupResponse>('/setting/?key=activated');
            setIsPopupEnabled(data.data.value);
        } catch (error) {
            console.error('Error fetching popup status:', error);
        }
    };

    const togglePopupStatus = async (newStatus: boolean) => {
        try {
            await apiFetch('/setting/popup', {
                method: 'PUT',
                data: { popup: newStatus },
            });

            setIsPopupEnabled(newStatus);
        } catch (error) {
            console.error('Error toggling popup status:', error);
        }
    };

    useEffect(() => {
        fetchPopupStatus();
    }, []);

    return (
        <Card>
            <BlockStack gap="300">
                <Text variant="headingMd" as="h3">Enable/Disable Product Popup Functionality:</Text>
                <ButtonGroup>
                    <Button
                        onClick={() => togglePopupStatus(true)}
                        variant={isPopupEnabled ? 'primary' : 'secondary'}
                    >
                        True
                    </Button>
                    <Button
                        onClick={() => togglePopupStatus(false)}
                        variant={!isPopupEnabled ? 'primary' : 'secondary'}
                    >
                        False
                    </Button>
                </ButtonGroup>
            </BlockStack>
        </Card>
    );
}
