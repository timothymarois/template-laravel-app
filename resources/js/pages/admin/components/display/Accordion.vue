<template>
    <LayoutApp
        title="Components - Accordion"
        pageTitle="Accordion"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Accordion -->
            <Card>
                <CardHeader>
                    <CardTitle>Basic Accordion</CardTitle>
                    <CardDescription>Single-open accordion where only one item can be expanded at a time</CardDescription>
                </CardHeader>
                <CardContent>
                    <Accordion type="single" default-value="item-1">
                        <AccordionItem value="item-1">
                            <AccordionTrigger>Is it accessible?</AccordionTrigger>
                            <AccordionContent>
                                Yes. It adheres to the WAI-ARIA design pattern for accordions.
                                Keyboard navigation is fully supported, and the component
                                properly manages focus states and ARIA attributes.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="item-2">
                            <AccordionTrigger>Is it styled?</AccordionTrigger>
                            <AccordionContent>
                                Yes. It comes with default styles that match the other components
                                in the design system. You can customize the appearance using
                                Tailwind CSS classes.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="item-3">
                            <AccordionTrigger>Is it animated?</AccordionTrigger>
                            <AccordionContent>
                                Yes. The accordion uses smooth height transitions for expanding
                                and collapsing content, and the chevron icon rotates to indicate
                                the open/closed state.
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>

            <!-- Multiple Open -->
            <Card>
                <CardHeader>
                    <CardTitle>Multiple Open</CardTitle>
                    <CardDescription>Allow multiple accordion items to be open simultaneously</CardDescription>
                </CardHeader>
                <CardContent>
                    <Accordion type="multiple" :default-value="['feature-1', 'feature-3']">
                        <AccordionItem value="feature-1">
                            <AccordionTrigger>Feature One</AccordionTrigger>
                            <AccordionContent>
                                This feature allows you to manage your tasks efficiently.
                                Create, edit, and organize your tasks with ease using our
                                intuitive interface.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="feature-2">
                            <AccordionTrigger>Feature Two</AccordionTrigger>
                            <AccordionContent>
                                Collaborate with your team members in real-time. Share
                                documents, leave comments, and track changes together.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="feature-3">
                            <AccordionTrigger>Feature Three</AccordionTrigger>
                            <AccordionContent>
                                Generate detailed reports and analytics to track your
                                progress. Export data in multiple formats for further analysis.
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>

            <!-- Non-collapsible -->
            <Card>
                <CardHeader>
                    <CardTitle>Non-collapsible</CardTitle>
                    <CardDescription>Keep at least one item open at all times</CardDescription>
                </CardHeader>
                <CardContent>
                    <Accordion type="single" default-value="always-1" :collapsible="false">
                        <AccordionItem value="always-1">
                            <AccordionTrigger>First Section</AccordionTrigger>
                            <AccordionContent>
                                This accordion is non-collapsible. Once an item is open,
                                clicking it again won't close it. You must click another
                                item to switch the expanded section.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="always-2">
                            <AccordionTrigger>Second Section</AccordionTrigger>
                            <AccordionContent>
                                This ensures that users always have content visible,
                                which can be useful for important information that
                                should remain accessible.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="always-3">
                            <AccordionTrigger>Third Section</AccordionTrigger>
                            <AccordionContent>
                                The non-collapsible behavior is helpful for
                                wizards, step-by-step guides, or navigation menus
                                where context should be maintained.
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>

            <!-- Controlled Accordion -->
            <Card>
                <CardHeader>
                    <CardTitle>Controlled Accordion</CardTitle>
                    <CardDescription>Programmatically control which items are open</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex gap-2 flex-wrap">
                        <Button variant="outline" @click="openItem = 'controlled-1'">Open Item 1</Button>
                        <Button variant="outline" @click="openItem = 'controlled-2'">Open Item 2</Button>
                        <Button variant="outline" @click="openItem = 'controlled-3'">Open Item 3</Button>
                        <Button variant="outline" @click="openItem = ''">Close All</Button>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        Currently open: <code class="bg-muted px-1 py-0.5 rounded">{{ openItem || 'none' }}</code>
                    </p>
                    <Accordion type="single" v-model="openItem" collapsible>
                        <AccordionItem value="controlled-1">
                            <AccordionTrigger>Controlled Item 1</AccordionTrigger>
                            <AccordionContent>
                                This accordion is controlled via v-model. You can
                                programmatically open any item using the buttons above.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="controlled-2">
                            <AccordionTrigger>Controlled Item 2</AccordionTrigger>
                            <AccordionContent>
                                The state is synchronized with the parent component,
                                allowing you to respond to changes or update the
                                accordion based on external events.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="controlled-3">
                            <AccordionTrigger>Controlled Item 3</AccordionTrigger>
                            <AccordionContent>
                                This pattern is useful for building wizards,
                                documentation browsers, or any UI where accordion
                                state needs to be managed externally.
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>

            <!-- FAQ Example -->
            <Card>
                <CardHeader>
                    <CardTitle>FAQ Section</CardTitle>
                    <CardDescription>Common use case for accordions: Frequently Asked Questions</CardDescription>
                </CardHeader>
                <CardContent>
                    <Accordion type="single" collapsible>
                        <AccordionItem value="faq-1">
                            <AccordionTrigger>How do I create an account?</AccordionTrigger>
                            <AccordionContent>
                                <p>Creating an account is simple:</p>
                                <ol class="list-decimal list-inside mt-2 space-y-1 text-muted-foreground">
                                    <li>Click the "Sign Up" button in the top right corner</li>
                                    <li>Enter your email address and create a password</li>
                                    <li>Verify your email by clicking the link we send you</li>
                                    <li>Complete your profile information</li>
                                </ol>
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="faq-2">
                            <AccordionTrigger>What payment methods do you accept?</AccordionTrigger>
                            <AccordionContent>
                                <p>We accept a variety of payment methods including:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-muted-foreground">
                                    <li>Credit and debit cards (Visa, Mastercard, American Express)</li>
                                    <li>PayPal</li>
                                    <li>Bank transfers</li>
                                    <li>Apple Pay and Google Pay</li>
                                </ul>
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="faq-3">
                            <AccordionTrigger>How can I contact support?</AccordionTrigger>
                            <AccordionContent>
                                <p>Our support team is available 24/7. You can reach us through:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-muted-foreground">
                                    <li>Email: support@example.com</li>
                                    <li>Live chat on our website</li>
                                    <li>Phone: 1-800-EXAMPLE</li>
                                    <li>Social media: @example on Twitter</li>
                                </ul>
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="faq-4">
                            <AccordionTrigger>What is your refund policy?</AccordionTrigger>
                            <AccordionContent>
                                We offer a 30-day money-back guarantee on all purchases.
                                If you're not satisfied with your purchase, simply contact
                                our support team within 30 days of your purchase date for
                                a full refund. No questions asked.
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>

            <!-- Accordion with Rich Content -->
            <Card>
                <CardHeader>
                    <CardTitle>Rich Content</CardTitle>
                    <CardDescription>Accordion items can contain any type of content</CardDescription>
                </CardHeader>
                <CardContent>
                    <Accordion type="single" collapsible>
                        <AccordionItem value="rich-1">
                            <AccordionTrigger>User Profile</AccordionTrigger>
                            <AccordionContent>
                                <div class="flex items-center gap-4">
                                    <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center">
                                        <User class="size-6 text-primary" />
                                    </div>
                                    <div>
                                        <h4 class="font-medium">John Doe</h4>
                                        <p class="text-sm text-muted-foreground">john@example.com</p>
                                    </div>
                                    <Button variant="outline" class="ml-auto">Edit</Button>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="rich-2">
                            <AccordionTrigger>Statistics</AccordionTrigger>
                            <AccordionContent>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-primary">1,234</p>
                                        <p class="text-sm text-muted-foreground">Users</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-primary">567</p>
                                        <p class="text-sm text-muted-foreground">Orders</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-primary">89%</p>
                                        <p class="text-sm text-muted-foreground">Satisfaction</p>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="rich-3">
                            <AccordionTrigger>Form Example</AccordionTrigger>
                            <AccordionContent>
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium">Email</label>
                                        <Input type="email" placeholder="Enter your email" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium">Name</label>
                                        <Input type="text" placeholder="Enter your name" />
                                    </div>
                                    <Button>Submit</Button>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>

            <!-- Disabled Items -->
            <Card>
                <CardHeader>
                    <CardTitle>Disabled Items</CardTitle>
                    <CardDescription>Individual accordion items can be disabled</CardDescription>
                </CardHeader>
                <CardContent>
                    <Accordion type="single" default-value="enabled-1" collapsible>
                        <AccordionItem value="enabled-1">
                            <AccordionTrigger>Enabled Item</AccordionTrigger>
                            <AccordionContent>
                                This item is enabled and can be clicked to expand or collapse.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="disabled-1" disabled>
                            <AccordionTrigger>Disabled Item</AccordionTrigger>
                            <AccordionContent>
                                This content won't be visible because the item is disabled.
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="enabled-2">
                            <AccordionTrigger>Another Enabled Item</AccordionTrigger>
                            <AccordionContent>
                                This item is also enabled. Disabled items can be useful
                                when certain sections should be temporarily inaccessible
                                based on user permissions or application state.
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/form';
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@/components/ui/accordion';
import { User } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const openItem = ref('controlled-1');
</script>
