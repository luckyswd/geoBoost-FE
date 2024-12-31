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
        {id: 'holidayMapping', content: 'Product holiday Mapping'},
        {id: 'settings', content: 'Settings'},
    ];

    return (
        <Page>
            <Tabs tabs={tabs} selected={selected} onSelect={handleTabChange}>
                <TabContent selectedTab={selected}/>
            </Tabs>
        </Page>
    );
}

export default TabsComponent;
