"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Switch } from "@/components/ui/switch"
import { Separator } from "@/components/ui/separator"
import { Save, User, Bell, Shield } from "lucide-react"

export default function ProfilePage() {
  const [isSaving, setIsSaving] = useState(false)
  const [profile, setProfile] = useState({
    firstName: "John",
    lastName: "Doe",
    email: "john@example.com",
    phone: "(555) 123-4567",
    preferredZone: "dining-room",
    dietaryRestrictions: "None",
    specialNotes: "",
    emailNotifications: true,
    smsNotifications: false,
    marketingEmails: true,
  })

  const handleSave = async () => {
    setIsSaving(true)
    // Mock save - in production this would update the database
    await new Promise(resolve => setTimeout(resolve, 1000))
    setIsSaving(false)
  }

  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div>
        <h1 className="font-serif text-2xl md:text-3xl text-foreground">Profile Settings</h1>
        <p className="text-muted-foreground mt-1">
          Manage your account and dining preferences
        </p>
      </div>

      <div className="grid gap-8 max-w-2xl">
        {/* Personal Information */}
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <User className="h-5 w-5 text-primary" />
              <CardTitle className="font-serif text-lg">Personal Information</CardTitle>
            </div>
            <CardDescription>
              Update your personal details and contact information
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="firstName">First Name</Label>
                <Input
                  id="firstName"
                  value={profile.firstName}
                  onChange={(e) => setProfile({ ...profile, firstName: e.target.value })}
                  className="bg-background"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="lastName">Last Name</Label>
                <Input
                  id="lastName"
                  value={profile.lastName}
                  onChange={(e) => setProfile({ ...profile, lastName: e.target.value })}
                  className="bg-background"
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="email">Email Address</Label>
              <Input
                id="email"
                type="email"
                value={profile.email}
                onChange={(e) => setProfile({ ...profile, email: e.target.value })}
                className="bg-background"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="phone">Phone Number</Label>
              <Input
                id="phone"
                type="tel"
                value={profile.phone}
                onChange={(e) => setProfile({ ...profile, phone: e.target.value })}
                className="bg-background"
              />
            </div>
          </CardContent>
        </Card>

        {/* Dining Preferences */}
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <span className="font-serif text-primary text-lg">E</span>
              <CardTitle className="font-serif text-lg">Dining Preferences</CardTitle>
            </div>
            <CardDescription>
              Help us personalize your dining experience
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="preferredZone">Preferred Dining Zone</Label>
              <Select
                value={profile.preferredZone}
                onValueChange={(value) => setProfile({ ...profile, preferredZone: value })}
              >
                <SelectTrigger className="bg-background">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="patio">The Patio</SelectItem>
                  <SelectItem value="dining-room">Main Dining Room</SelectItem>
                  <SelectItem value="bar">The Bar</SelectItem>
                  <SelectItem value="no-preference">No Preference</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="dietary">Dietary Restrictions</Label>
              <Select
                value={profile.dietaryRestrictions}
                onValueChange={(value) => setProfile({ ...profile, dietaryRestrictions: value })}
              >
                <SelectTrigger className="bg-background">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="None">None</SelectItem>
                  <SelectItem value="Vegetarian">Vegetarian</SelectItem>
                  <SelectItem value="Vegan">Vegan</SelectItem>
                  <SelectItem value="Gluten-Free">Gluten-Free</SelectItem>
                  <SelectItem value="Dairy-Free">Dairy-Free</SelectItem>
                  <SelectItem value="Nut Allergy">Nut Allergy</SelectItem>
                  <SelectItem value="Other">Other (specify below)</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="notes">Special Notes</Label>
              <Textarea
                id="notes"
                value={profile.specialNotes}
                onChange={(e) => setProfile({ ...profile, specialNotes: e.target.value })}
                placeholder="Any allergies, accessibility needs, or special requests..."
                className="bg-background min-h-[100px]"
              />
            </div>
          </CardContent>
        </Card>

        {/* Notifications */}
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <Bell className="h-5 w-5 text-primary" />
              <CardTitle className="font-serif text-lg">Notifications</CardTitle>
            </div>
            <CardDescription>
              Choose how you want to receive updates
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex items-center justify-between">
              <div className="space-y-0.5">
                <Label>Email Notifications</Label>
                <p className="text-sm text-muted-foreground">
                  Receive reservation confirmations and reminders
                </p>
              </div>
              <Switch
                checked={profile.emailNotifications}
                onCheckedChange={(checked) => setProfile({ ...profile, emailNotifications: checked })}
              />
            </div>
            
            <Separator />

            <div className="flex items-center justify-between">
              <div className="space-y-0.5">
                <Label>SMS Notifications</Label>
                <p className="text-sm text-muted-foreground">
                  Receive text message reminders
                </p>
              </div>
              <Switch
                checked={profile.smsNotifications}
                onCheckedChange={(checked) => setProfile({ ...profile, smsNotifications: checked })}
              />
            </div>

            <Separator />

            <div className="flex items-center justify-between">
              <div className="space-y-0.5">
                <Label>Marketing Emails</Label>
                <p className="text-sm text-muted-foreground">
                  Receive news about special events and offers
                </p>
              </div>
              <Switch
                checked={profile.marketingEmails}
                onCheckedChange={(checked) => setProfile({ ...profile, marketingEmails: checked })}
              />
            </div>
          </CardContent>
        </Card>

        {/* Security */}
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <Shield className="h-5 w-5 text-primary" />
              <CardTitle className="font-serif text-lg">Security</CardTitle>
            </div>
            <CardDescription>
              Manage your account security settings
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <Button variant="outline" className="w-full sm:w-auto">
              Change Password
            </Button>
          </CardContent>
        </Card>

        {/* Save Button */}
        <div className="flex justify-end">
          <Button
            onClick={handleSave}
            disabled={isSaving}
            className="bg-primary text-primary-foreground hover:bg-primary/90"
          >
            <Save className="h-4 w-4 mr-2" />
            {isSaving ? "Saving..." : "Save Changes"}
          </Button>
        </div>
      </div>
    </div>
  )
}
