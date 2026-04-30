import {
    Table,
    For,
    Button,
    HStack,
    Tag,
    Pagination,
    VStack,
    ButtonGroup,
    IconButton,
    Heading,
} from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import { FaRegTrashAlt } from "react-icons/fa";
import { RiEdit2Fill } from "react-icons/ri";
import UserDetailDrawer from "@/components/common/drawers/UserDetailDrawer";
import { useState } from "react";
import UserDeleteDialog from "@/components/common/dialogs/UserDeleteDialog";

const USER_STATUSES = {
    active: {
        type: "ACTIVE",
        label: "Active",
        color: "#52D880",
        bg: "#21AF52",
    },
    inactive: {
        type: "INACTIVE",
        label: "In-active",
        color: "#E9677F",
        bg: "#B71F3B",
    },
};

const items = [
    {
        id: 1,
        fullname: "test1",
        phoneNumber: "0123456789",
        email: "test1@gmail.com",
        status: USER_STATUSES.active,
        actions: ["edit", "delete"],
    },
    {
        id: 2,
        fullname: "test2",
        phoneNumber: "0123456789",
        email: "test2@gmail.com",
        status: USER_STATUSES.inactive,
        actions: ["edit", "delete"],
    },
    {
        id: 3,
        fullname: "test3",
        phoneNumber: "0123456789",
        email: "test3@gmail.com",
        status: USER_STATUSES.active,
        actions: ["edit", "delete"],
    },
    {
        id: 4,
        fullname: "test4",
        phoneNumber: "0123456789",
        email: "test4@gmail.com",
        status: USER_STATUSES.inactive,
        actions: ["edit", "delete"],
    },
    {
        id: 5,
        fullname: "test5",
        phoneNumber: "0123456789",
        email: "test5@gmail.com",
        status: USER_STATUSES.active,
        actions: ["edit", "delete"],
    },
];

function UserPage() {
    const [openDetail, setOpenDetail] = useState(false);
    const [openForDelete, setOpenForDelete] = useState(false);

    return (
        <VStack gap={8}>
            <Heading alignSelf={"start"} color={"brand.400"} fontSize={48}>
                Users Management
            </Heading>
            <Table.Root variant={"outline"}>
                {/* Table Header */}
                <Table.Header>
                    <Table.Row gap={2}>
                        <Table.ColumnHeader color="white" bg={"dark.800"}>
                            ID
                        </Table.ColumnHeader>
                        <Table.ColumnHeader color="white" bg={"dark.800"}>
                            Full name
                        </Table.ColumnHeader>
                        <Table.ColumnHeader color="white" bg={"dark.800"}>
                            Phone number
                        </Table.ColumnHeader>
                        <Table.ColumnHeader color="white" bg={"dark.800"}>
                            Email
                        </Table.ColumnHeader>
                        <Table.ColumnHeader color="white" bg={"dark.800"}>
                            Status
                        </Table.ColumnHeader>
                        <Table.ColumnHeader color="white" bg={"dark.800"}>
                            Actions
                        </Table.ColumnHeader>
                    </Table.Row>
                </Table.Header>

                {/* Table Body */}
                <Table.Body borderColor="gray.300">
                    <For each={items}>
                        {(item, index) => {
                            return (
                                <Table.Row key={index}>
                                    <Table.Cell>{item.id}</Table.Cell>
                                    <Table.Cell>{item.fullname}</Table.Cell>
                                    <Table.Cell>{item.phoneNumber}</Table.Cell>
                                    <Table.Cell>{item.email}</Table.Cell>
                                    <Table.Cell>
                                        <Tag.Root variant={"line"} bg={item.status.color}>
                                            <Tag.Label p={1} color={"white"}>
                                                {item.status.label}
                                            </Tag.Label>
                                        </Tag.Root>
                                    </Table.Cell>
                                    <Table.Cell>
                                        <HStack>
                                            <Button
                                                color={"skyblue.500"}
                                                bg={"dark.700"}
                                                onClick={() => setOpenDetail(true)}
                                            >
                                                <RiEdit2Fill />
                                            </Button>
                                            <Button
                                                color={"brand.500"}
                                                bg={"dark.700"}
                                                onClick={() => setOpenForDelete(true)}
                                            >
                                                <FaRegTrashAlt />
                                            </Button>
                                        </HStack>
                                    </Table.Cell>
                                </Table.Row>
                            );
                        }}
                    </For>
                </Table.Body>
            </Table.Root>

            {/* Pagination */}
            <Pagination.Root count={items.length * 5} pageSize={5} page={1}>
                <ButtonGroup variant="ghost" size="sm" wrap="wrap">
                    <Pagination.PrevTrigger asChild>
                        <IconButton>
                            <LuChevronLeft />
                        </IconButton>
                    </Pagination.PrevTrigger>

                    <Pagination.Items
                        render={(page) => (
                            <IconButton variant={{ base: "ghost", _selected: "outline" }}>
                                {page.value}
                            </IconButton>
                        )}
                    />

                    <Pagination.NextTrigger asChild>
                        <IconButton>
                            <LuChevronRight />
                        </IconButton>
                    </Pagination.NextTrigger>
                </ButtonGroup>
            </Pagination.Root>

            {/* Open User Detail Drawer */}
            <UserDetailDrawer isOpen={openDetail} onClose={() => setOpenDetail(false)} />

            {/* Open User Delete Dialog */}
            <UserDeleteDialog isOpen={openForDelete} onClose={() => setOpenForDelete(false)} />
        </VStack>
    );
}

export default UserPage;
