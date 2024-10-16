import {ButtonGroup, Button, Text, BlockStack, Card} from '@shopify/polaris';
import {useState} from 'react';
import {apiFetch} from "../../../api";
import {useSettings} from "../../../Provaiders/SettingsContext";
import {ACTIVATED} from "./type";

export default function PopupActivated() {
    const {settings} = useSettings();
    const [activated, setActivated] = useState<boolean>(!!settings?.data?.activated);
    const [disabledButton, setDisabledButton] = useState<boolean>(false);

    const togglePopup = async (value: boolean) => {
        if (activated === value) {
            return;
        }

        setActivated(value);
        setDisabledButton(true);

        await apiFetch(`/setting/set`, {
            method: 'PUT',
            data: {
                key: ACTIVATED,
                value: value,
            },
        });

        setDisabledButton(false);
    };

    return (
        <Card>
            <BlockStack gap="300">
                <Text variant="headingMd" as="h3">Enable/Disable Product Popup</Text>
                <ButtonGroup>
                    <Button
                        onClick={() => togglePopup(true)}
                        variant={activated ? 'primary' : 'secondary'}
                        disabled={disabledButton}
                    >
                        True
                    </Button>
                    <Button
                        onClick={() => togglePopup(false)}
                        variant={!activated ? 'primary' : 'secondary'}
                        disabled={disabledButton}
                    >
                        False
                    </Button>
                </ButtonGroup>
            </BlockStack>
        </Card>
    );
}
