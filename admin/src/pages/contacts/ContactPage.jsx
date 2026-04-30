import { IconButton, VStack, Pagination, ButtonGroup, Heading } from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import ContactGrid from "@/components/common/grids/ContactGrid";

export const contacts = [
    {
        id: 1,
        name: "Emily Tran",
        email: "emily.tran@gmail.com",
        phone: "0412345678",
        address: "Melbourne, VIC",
        description:
            "I have social anxiety and find it difficult to communicate in everyday situations like ordering food or asking for help. I want to use Scenari-Aid to practice real-life conversations.",
    },
    {
        id: 2,
        name: "James Walker",
        email: "james.walker@hotmail.com",
        description:
            "I struggle with job interviews and often get nervous. I am looking for realistic interview scenarios to improve my confidence.",
    },
    {
        id: 3,
        name: "Nguyen Minh Anh",
        email: "minhanh.nguyen@gmail.com",
        phone: "0423456789",
        description:
            "English is not my first language. I want to practice daily conversations like shopping and talking to coworkers.",
    },
    {
        id: 4,
        name: "Sophie Brown",
        email: "sophie.brown@yahoo.com",
        address: "Ballarat, VIC",
        description:
            "I am recovering from a stroke and need help rebuilding my communication skills step by step.",
    },
    {
        id: 5,
        name: "Daniel Lee",
        email: "daniel.lee@gmail.com",
        phone: "0400111222",
        description:
            "I have a stuttering problem and want to practice speaking fluently in social situations.",
    },
    {
        id: 6,
        name: "Michael Johnson",
        email: "m.johnson@gmail.com",
        description:
            "I have been unemployed for a long time and want to practice job interviews and workplace communication.",
    },
    {
        id: 7,
        name: "Hannah Wilson",
        email: "hannah.wilson@gmail.com",
        phone: "0433222111",
        address: "Geelong, VIC",
        description:
            "I feel nervous making phone calls like booking appointments or talking to customer service.",
    },
    {
        id: 8,
        name: "Ali Hassan",
        email: "ali.hassan@gmail.com",
        description:
            "I recently moved to Australia and need help adapting to everyday social interactions and cultural communication.",
    },
    {
        id: 9,
        name: "Lucas Martin",
        email: "lucas.martin@gmail.com",
        phone: "0455667788",
        description:
            "I want to practice conversations in cafes and restaurants because I feel uncomfortable speaking in public.",
    },
    {
        id: 10,
        name: "Jessica Taylor",
        email: "jessica.taylor@gmail.com",
        address: "Sydney, NSW",
        description:
            "I need to improve my communication skills for professional environments like meetings and presentations.",
    },
    {
        id: 11,
        name: "Chris Nguyen",
        email: "chris.nguyen@gmail.com",
        phone: "0411999888",
        description:
            "I want to simulate real-life scenarios like booking flights or talking to service staff.",
    },
    {
        id: 12,
        name: "Olivia Smith",
        email: "olivia.smith@gmail.com",
        description:
            "I lack confidence in social conversations and want structured practice through guided scenarios.",
    },
];

function ContactPage() {
    return (
        <VStack gap={6}>
            <Heading alignSelf={"start"} color={"brand.400"} fontSize={48}>
                Contact Management
            </Heading>
            <ContactGrid />
            {/* Pagination */}
            <Pagination.Root count={contacts.length * 5} pageSize={5} page={1}>
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
        </VStack>
    );
}

export default ContactPage;
