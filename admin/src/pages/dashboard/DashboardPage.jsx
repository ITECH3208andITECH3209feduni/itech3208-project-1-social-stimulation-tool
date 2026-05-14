import CardPerformance from "@/components/common/cards/CardPerformance";
import { Box, Text, Heading, For, HStack, VStack } from "@chakra-ui/react";
import React from "react";
import { AiFillLike } from "react-icons/ai";
import { IoMdPlayCircle } from "react-icons/io";
import { PiCursorClickFill } from "react-icons/pi";
import { TbWaveSawTool } from "react-icons/tb";
import { FiUsers, FiUserCheck, FiUserPlus, FiUserX } from "react-icons/fi";
import { MdOutlineCategory } from "react-icons/md";
import { HiOutlineGlobeAlt } from "react-icons/hi";

function StatSection({ title, items }) {
    return (
        <VStack align="start" w="full" gap={3}>
            <Text
                fontWeight="semibold"
                fontSize="sm"
                color="gray.500"
                textTransform="uppercase"
                letterSpacing="wide"
            >
                {title}
            </Text>
            <HStack w="full" gap={4} align="stretch">
                <For each={items}>
                    {(item) => (
                        <CardPerformance
                            key={item.id}
                            title={item.title}
                            records={item.records}
                            icon={item.icon}
                        />
                    )}
                </For>
            </HStack>
        </VStack>
    );
}

function DashboardPage() {
    const videoStats = [
        { id: 1, title: "Videos Created", records: 1808, icon: <IoMdPlayCircle /> },
        { id: 2, title: "Total Plays", records: 1002, icon: <TbWaveSawTool /> },
        { id: 3, title: "Total Likes", records: 542, icon: <AiFillLike /> },
        { id: 4, title: "Total Clicks", records: 487, icon: <PiCursorClickFill /> },
    ];

    const userStats = [
        { id: 1, title: "Total Registered", records: 1284, icon: <FiUsers /> },
        { id: 2, title: "Active This Month", records: 874, icon: <FiUserCheck /> },
        { id: 3, title: "New This Month", records: 63, icon: <FiUserPlus /> },
        { id: 4, title: "Inactive (90d+)", records: 147, icon: <FiUserX /> },
    ];

    const scenarioStats = [
        { id: 1, title: "Total Plays", records: 9402, icon: <IoMdPlayCircle /> },
        { id: 2, title: "Avg. Per User", records: 7, icon: <TbWaveSawTool /> },
        { id: 3, title: "Categories", records: 8, icon: <MdOutlineCategory /> },
        { id: 4, title: "Completion Rate", records: "74%", icon: <PiCursorClickFill /> },
    ];

    const geoStats = [
        { id: 1, title: "Countries", records: 38, icon: <HiOutlineGlobeAlt /> },
        { id: 2, title: "Top Country", records: "AU", icon: <HiOutlineGlobeAlt /> },
        { id: 3, title: "Intl. Users", records: 410, icon: <FiUsers /> },
        { id: 4, title: "Languages", records: 12, icon: <MdOutlineCategory /> },
    ];

    return (
        <Box spaceY={8}>
            <Heading color="dark.500" fontSize="30px" fontWeight="bold">
                Dashboard
            </Heading>

            <StatSection title="Video Overview" items={videoStats} />
            <StatSection title="User Management" items={userStats} />
            <StatSection title="Scenario Performance" items={scenarioStats} />
            <StatSection title="Geographic & Demographic Insights" items={geoStats} />
        </Box>
    );
}

export default DashboardPage;
