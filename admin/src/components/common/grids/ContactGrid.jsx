import { Grid, GridItem, For } from "@chakra-ui/react";
import ContactCard from "../cards/ContactCard";

function ContactGrid({ contacts }) {
    return (
        <Grid gap={4} templateColumns={"repeat(3, 1fr)"}>
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
