import openai
openai.api_key = "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"

model = openai.Model.list()

# for i in model["data"]:
#     print(i["id"])
# print(len(model["data"]))

a = openai.Completion.create(
  model="text-davinci-003",
  prompt="Tell me a joke",
  max_tokens=25,
  temperature=0.5
)

print(a)