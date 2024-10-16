import React, {useState} from 'react';
import {Tabs, Page} from '@shopify/polaris';
import TabContent from "./TabContent";

function TabsComponent() {
    const [selected, setSelected] = useState(0);

    const handleTabChange = (selectedTabIndex: number) => {
        setSelected(selectedTabIndex);
    };

    const tabs = [
        {id: 'dashboard', content: 'Dashboard'},
        {id: 'holidays', content: 'Holidays'},
        {id: 'settings', content: 'Settings'},
    ];

    return (
        <Tabs tabs={tabs} selected={selected} onSelect={handleTabChange}>
            <Page>
                <TabContent selectedTab={selected}/>
            </Page>
        </Tabs>
    );
}

export default TabsComponent;
