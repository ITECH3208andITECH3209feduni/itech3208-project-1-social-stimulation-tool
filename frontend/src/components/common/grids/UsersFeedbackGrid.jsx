import { Grid, GridItem } from "@chakra-ui/react";
import FeedbackCard from "../cards/FeedbackCard";

function UsersFeedbackGrid() {
    return (
        <Grid gap={8} templateRows="repeat(2, 1fr)" templateColumns="repeat(3, 1fr)">
            <GridItem>
                <FeedbackCard />
            </GridItem>
            <GridItem>
                <FeedbackCard />
            </GridItem>
            <GridItem>
                <FeedbackCard />
            </GridItem>
            <GridItem>
                <FeedbackCard />
            </GridItem>
            <GridItem>
                <FeedbackCard />
            </GridItem>
            <GridItem>
                <FeedbackCard />
            </GridItem>
        </Grid>
    );
}

export default UsersFeedbackGrid;
