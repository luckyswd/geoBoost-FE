import {ButtonGroup, Button, Text, BlockStack, Card} from '@shopify/polaris';
import {useState} from 'react';
import {apiFetch} from "../../../api";
import {useSettings} from "../../../Provaiders/SettingsContext";
import {ACTIVATED} from "./type";

interface PopupResponse {
    data: {
        value: boolean;
    };
}

export default function PopupActivated() {
    const {settings} = useSettings();
    const [activated, setActivated] = useState<boolean>(!!settings?.data?.activated);

    const togglePopup = async (value: boolean) => {
        setActivated(value);

        await apiFetch(`/setting/set`, {
            method: 'PUT',
            data: {
                key: ACTIVATED,
                value: value,
            },
        });
    };

    return (
        <Card>
            <BlockStack gap="300">
                <Text variant="headingMd" as="h3">Enable/Disable Product Popup</Text>
                <ButtonGroup>
                    <Button
                        onClick={() => togglePopup(true)}
                        variant={activated ? 'primary' : 'secondary'}
                    >
                        True
                    </Button>
                    <Button
                        onClick={() => togglePopup(false)}
                        variant={!activated ? 'primary' : 'secondary'}
                    >
                        False
                    </Button>
                </ButtonGroup>
            </BlockStack>
        </Card>
    );
}
