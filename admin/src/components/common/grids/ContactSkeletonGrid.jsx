import { Grid, GridItem } from "@chakra-ui/react";
import ContactSkeletonCard from "../cards/ContactSkeletonCard";

function ContactSkeletonGrid({ count = 6 }) {
    return (
        <Grid gap={4} templateColumns={"repeat(3, 1fr)"}>
            {Array.from({ length: count }).map((_, i) => (
                <GridItem key={i}>
                    <ContactSkeletonCard />
                </GridItem>
            ))}
        </Grid>
    );
}

export default ContactSkeletonGrid;
