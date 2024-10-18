import {createContext, useContext, useState, useEffect} from "react";
import {ApiV1Response} from "../type/global";
import {apiFetch} from "../api";
import {Settings} from "../components/Tabs/type/SettingType";

const SettingsContext = createContext<{
    settings: ApiV1Response<Settings> | undefined;
    setSettings: (settings: ApiV1Response<Settings>) => void;
} | undefined>(undefined);

export const SettingsProvider = ({children}: { children: React.ReactNode }) => {
    const [settings, setSettings] = useState<ApiV1Response<Settings>>();

    useEffect(() => {
        apiFetch<ApiV1Response<Settings>>('/setting/?key=all')
            .then((response) => {
                setSettings(response);
            })
            .catch(() => {
                setSettings({errors: "Failed to fetch settings"});
            });
    }, []);

    return (
        <SettingsContext.Provider value={{settings, setSettings}}>
            {children}
        </SettingsContext.Provider>
    );
};

export const useSettings = () => {
    const context = useContext(SettingsContext);

    if (!context) {
        throw new Error("useSettings must be used within a SettingsProvider");
    }

    return context;
};
