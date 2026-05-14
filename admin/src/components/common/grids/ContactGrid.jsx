import { Card, Grid, GridItem, For } from "@chakra-ui/react";
import ContactCard from "../cards/ContactCard";
import { STATUS } from "@/components/badges/StatusBadge";

export const contacts = [
    {
        id: 1,
        name: "Emily Tran",
        email: "emily.tran@gmail.com",
        phone: "0412345678",
        address: "Melbourne, VIC",
        status: STATUS.PENDING.value,
        description:
            "I have social anxiety and find it difficult to communicate in everyday situations like ordering food or asking for help. I want to use Scenari-Aid to practice real-life conversations.",
    },
    {
        id: 2,
        name: "James Walker",
        email: "james.walker@hotmail.com",
        status: STATUS.IN_PROGRESS.value,
        description:
            "I struggle with job interviews and often get nervous. I am looking for realistic interview scenarios to improve my confidence.",
    },
    {
        id: 3,
        name: "Nguyen Minh Anh",
        email: "minhanh.nguyen@gmail.com",
        phone: "0423456789",
        status: STATUS.RESOLVED.value,
        description:
            "English is not my first language. I want to practice daily conversations like shopping and talking to coworkers.",
    },
    {
        id: 4,
        name: "Sophie Brown",
        email: "sophie.brown@yahoo.com",
        address: "Ballarat, VIC",
        status: STATUS.PENDING.value,
        description:
            "I am recovering from a stroke and need help rebuilding my communication skills step by step.",
    },
    {
        id: 5,
        name: "Daniel Lee",
        email: "daniel.lee@gmail.com",
        phone: "0400111222",
        status: STATUS.IN_PROGRESS.value,
        description:
            "I have a stuttering problem and want to practice speaking fluently in social situations.",
    },
    {
        id: 6,
        name: "Michael Johnson",
        email: "m.johnson@gmail.com",
        status: STATUS.REJECTED.value,
        description:
            "I have been unemployed for a long time and want to practice job interviews and workplace communication.",
    },
    {
        id: 7,
        name: "Hannah Wilson",
        email: "hannah.wilson@gmail.com",
        phone: "0433222111",
        address: "Geelong, VIC",
        status: STATUS.PENDING.value,
        description:
            "I feel nervous making phone calls like booking appointments or talking to customer service.",
    },
    {
        id: 8,
        name: "Ali Hassan",
        email: "ali.hassan@gmail.com",
        status: STATUS.REJECTED.value,
        description:
            "I recently moved to Australia and need help adapting to everyday social interactions and cultural communication.",
    },
    {
        id: 9,
        name: "Lucas Martin",
        email: "lucas.martin@gmail.com",
        phone: "0455667788",
        status: STATUS.REJECTED.value,
        description:
            "I want to practice conversations in cafes and restaurants because I feel uncomfortable speaking in public.",
    },
    {
        id: 10,
        name: "Jessica Taylor",
        email: "jessica.taylor@gmail.com",
        address: "Sydney, NSW",
        status: STATUS.RESOLVED.value,
        description:
            "I need to improve my communication skills for professional environments like meetings and presentations.",
    },
    {
        id: 11,
        name: "Chris Nguyen",
        email: "chris.nguyen@gmail.com",
        phone: "0411999888",
        status: STATUS.REJECTED.value,
        description:
            "I want to simulate real-life scenarios like booking flights or talking to service staff.",
    },
    {
        id: 12,
        name: "Olivia Smith",
        email: "olivia.smith@gmail.com",
        status: STATUS.REJECTED.value,
        description:
            "I lack confidence in social conversations and want structured practice through guided scenarios.",
    },
];

function ContactGrid() {
    return (
        <Grid gap={4} templateColumns={"repeat(3, 1fr)"} templateRows={"repeat(4, 1fr)"}>
            <For each={contacts}>
                {(contact) => {
                    return (
                        <GridItem key={contact.id}>
                            <ContactCard contact={contact} />
                        </GridItem>
                    );
                }}
            </For>
        </Grid>
    );
}

export default ContactGrid;
