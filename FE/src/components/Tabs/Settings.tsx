import {BlockStack, SkeletonBodyText} from '@shopify/polaris';
import PopupActivated from "./Settings/PopupActivated";
import {useSettings} from "../../Provaiders/SettingsContext";

export default function Settings() {
    const {isLoading} = useSettings();

    return (
        <BlockStack gap="300">
            {isLoading ? (
                <SkeletonBodyText lines={24}/>
            ) : (
                <PopupActivated/>
            )}
        </BlockStack>
    );
}
